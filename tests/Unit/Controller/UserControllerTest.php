<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\Api\UserController;
use App\Entity\User;
use App\Dto\CreateUserRequest;
use App\Repository\UserRepository;
use App\Service\UserService;
use Psr\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

#[CoversClass(UserController::class)]
final class UserControllerTest extends TestCase
{
    private function makeUser(int $id = 1): User
    {
        $user = new User();
        $user->email = "user{$id}@test.com";
        $user->name = "User {$id}";
        $user->password_hash = 'hash';

        $ref = new \ReflectionProperty($user, 'id');
        $ref->setValue($user, $id);
        $ref = new \ReflectionProperty($user, 'created_at');
        $ref->setValue($user, new \DateTimeImmutable());
        $ref = new \ReflectionProperty($user, 'updated_at');
        $ref->setValue($user, new \DateTimeImmutable());

        return $user;
    }

    private function makeController(
        ?UserRepository $repo = null,
        ?UserService $service = null,
    ): UserController {
        $repo = $repo ?? $this->createMock(UserRepository::class);
        $service = $service ?? new UserService(
            $repo,
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LoggerInterface::class),
        );

        return new UserController($service, $repo);
    }

    private function mockRequest(array $queryParams = []): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($queryParams);
        return $request;
    }

    #[Test]
    public function indexReturnsJsonCollection(): void
    {
        $users = [$this->makeUser(1), $this->makeUser(2)];

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findActiveUsers')->willReturn($users);

        $controller = $this->makeController(repo: $repo);
        $response = $controller->index($this->mockRequest());

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertCount(2, $body['data']);
    }

    #[Test]
    public function showReturnsUserById(): void
    {
        $user = $this->makeUser(42);

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOrFail')->with(42)->willReturn($user);

        $controller = $this->makeController(repo: $repo);
        $response = $controller->show($this->mockRequest(), '42');

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(42, $body['data']['id']);
    }

    #[Test]
    public function createCallsServiceAndReturns201(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('persist')->willReturnCallback(function (User $user): void {
            // Simulate DB persist: set ID and timestamps via reflection
            $ref = new \ReflectionProperty($user, 'id');
            $ref->setValue($user, 99);
            $ref = new \ReflectionProperty($user, 'created_at');
            $ref->setValue($user, new \DateTimeImmutable());
            $ref = new \ReflectionProperty($user, 'updated_at');
            $ref->setValue($user, new \DateTimeImmutable());
        });

        $service = new UserService(
            $repo,
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LoggerInterface::class),
        );

        $dto = new CreateUserRequest('new@test.com', 'New User', 'password1');
        $controller = $this->makeController(repo: $repo, service: $service);
        $response = $controller->create($dto);

        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(99, $body['data']['id']);
    }

    #[Test]
    public function destroyReturnsNoContent(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())->method('delete')->with(5);

        $controller = $this->makeController(repo: $repo);
        $response = $controller->destroy('5');

        $this->assertSame(204, $response->getStatusCode());
    }
}
