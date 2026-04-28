<?php
declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Dto\CreateUserRequest;
use App\Entity\User;
use App\Event\UserCreated;
use App\Repository\UserRepository;
use App\Service\UserService;
use Psr\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(UserService::class)]
final class UserServiceTest extends TestCase
{
    #[Test]
    public function createUserPersistsAndDispatchesEvent(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $events = $this->createMock(EventDispatcherInterface::class);
        $events->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserCreated::class));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $service = new UserService($repo, $events, $logger);

        $dto = new CreateUserRequest(
            email: 'test@example.com',
            name: 'Jorge',
            password: 'secure-password-123',
        );

        $user = $service->createUser($dto);

        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('Jorge', $user->name);
        $this->assertTrue(password_verify('secure-password-123', $user->password_hash));
    }

    #[Test]
    public function findUserDelegatesToRepository(): void
    {
        $mockUser = new User();
        $mockUser->email = 'found@example.com';
        $mockUser->name = 'Found User';

        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())
            ->method('find')
            ->with(42)
            ->willReturn($mockUser);

        $events = $this->createMock(EventDispatcherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $service = new UserService($repo, $events, $logger);
        $result = $service->findUser(42);

        $this->assertSame($mockUser, $result);
    }

    #[Test]
    public function deleteUserCallsRepositoryAndLogs(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())
            ->method('delete')
            ->with(99);

        $events = $this->createMock(EventDispatcherInterface::class);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('User deleted', ['id' => 99]);

        $service = new UserService($repo, $events, $logger);
        $service->deleteUser(99);
    }
}
