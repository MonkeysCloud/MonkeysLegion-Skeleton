<?php
declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(AuthService::class)]
final class AuthServiceTest extends TestCase
{
    private function makeService(
        ?UserRepository $repo = null,
        ?LoggerInterface $logger = null,
    ): AuthService {
        return new AuthService(
            $repo ?? $this->createMock(UserRepository::class),
            $logger ?? $this->createMock(LoggerInterface::class),
        );
    }

    #[Test]
    public function attemptReturnsNullWhenUserNotFound(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn(null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $service = $this->makeService($repo, $logger);

        $this->assertNull($service->attempt('unknown@test.com', 'password'));
    }

    #[Test]
    public function attemptReturnsNullOnWrongPassword(): void
    {
        $user = new User();
        $user->email = 'test@test.com';
        $user->name = 'Test';
        $user->password_hash = password_hash('correct-password', PASSWORD_DEFAULT);

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn($user);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $service = $this->makeService($repo, $logger);

        $this->assertNull($service->attempt('test@test.com', 'wrong-password'));
    }

    #[Test]
    public function attemptReturnsUserOnValidCredentials(): void
    {
        $user = new User();
        $user->email = 'test@test.com';
        $user->name = 'Test';
        $user->password_hash = password_hash('my-password', PASSWORD_DEFAULT);

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn($user);

        $service = $this->makeService($repo);
        $result = $service->attempt('test@test.com', 'my-password');

        $this->assertSame($user, $result);
    }

    #[Test]
    public function invalidateTokensBumpsVersionAndPersists(): void
    {
        $user = new User();
        $user->email = 'test@test.com';
        $user->name = 'Test';
        $user->password_hash = 'hashed';

        $this->assertSame(1, $user->token_version);

        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())->method('persist')->with($user);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $service = $this->makeService($repo, $logger);
        $service->invalidateTokens($user);

        $this->assertSame(2, $user->token_version);
    }
}
