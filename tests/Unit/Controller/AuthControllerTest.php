<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\Api\AuthController;
use App\Dto\LoginRequest;
use App\Dto\CreateUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Psr\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

#[CoversClass(AuthController::class)]
final class AuthControllerTest extends TestCase
{
    private function makeController(
        ?UserRepository $repo = null,
        ?UserService $service = null,
    ): AuthController {
        $repo = $repo ?? $this->createMock(UserRepository::class);

        if ($service === null) {
            // UserService is final — must construct a real instance
            $service = new UserService(
                $repo,
                $this->createMock(EventDispatcherInterface::class),
                $this->createMock(LoggerInterface::class),
            );
        }

        return new AuthController($repo, $service);
    }

    #[Test]
    public function loginReturns401OnMissingUser(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn(null);

        $controller = $this->makeController(repo: $repo);
        $response = $controller->login(new LoginRequest('bad@test.com', 'password1'));

        $this->assertSame(401, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame('Invalid credentials', $body['error']);
    }

    #[Test]
    public function loginReturns401OnWrongPassword(): void
    {
        $user = new User();
        $user->email = 'test@test.com';
        $user->name = 'Test';
        $user->password_hash = password_hash('correct1', PASSWORD_DEFAULT);

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn($user);

        $controller = $this->makeController(repo: $repo);
        $response = $controller->login(new LoginRequest('test@test.com', 'wrong123'));

        $this->assertSame(401, $response->getStatusCode());
    }

    #[Test]
    public function loginReturns200OnValidCredentials(): void
    {
        $user = new User();
        $user->email = 'test@test.com';
        $user->name = 'Test';
        $user->password_hash = password_hash('correct1', PASSWORD_DEFAULT);

        $ref = new \ReflectionProperty($user, 'id');
        $ref->setValue($user, 5);

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn($user);

        $controller = $this->makeController(repo: $repo);
        $response = $controller->login(new LoginRequest('test@test.com', 'correct1'));

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(5, $body['data']['user_id']);
    }

    #[Test]
    public function registerReturns422WhenEmailExists(): void
    {
        $existing = new User();
        $existing->email = 'taken@test.com';
        $existing->name = 'Existing';

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn($existing);

        $controller = $this->makeController(repo: $repo);
        $response = $controller->register(new CreateUserRequest(
            'taken@test.com', 'Name', 'password1',
        ));

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame('Email already registered', $body['details']['email']);
    }

    #[Test]
    public function registerReturns201OnSuccess(): void
    {
        $newUser = new User();
        $newUser->email = 'new@test.com';
        $newUser->name = 'New';
        $newUser->password_hash = 'hash';

        $ref = new \ReflectionProperty($newUser, 'id');
        $ref->setValue($newUser, 99);

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findByEmail')->willReturn(null);
        $repo->method('persist');

        $service = new UserService(
            $repo,
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LoggerInterface::class),
        );

        // Override createUser to return our prepared user
        $controller = new AuthController($repo, $service);
        $response = $controller->register(new CreateUserRequest(
            'new@test.com', 'New', 'password1',
        ));

        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame('new@test.com', $body['data']['email']);
    }

    #[Test]
    public function logoutReturns200(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $controller = $this->makeController();
        $response = $controller->logout($request);

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame('Logged out successfully', $body['data']['message']);
    }
}
