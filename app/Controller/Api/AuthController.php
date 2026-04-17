<?php
declare(strict_types=1);

namespace App\Controller\Api;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Router\Attributes\Throttle;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Http\Message\Response;

use Psr\Http\Message\ServerRequestInterface;

use App\Dto\LoginRequest;
use App\Dto\CreateUserRequest;
use App\Repository\UserRepository;
use App\Service\UserService;

/**
 * Authentication API — login, register, refresh, logout.
 */
#[RoutePrefix('/api/v2/auth')]
#[Middleware(['cors'])]
final class AuthController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserService $userService,
    ) {}

    #[Route('POST', '/login', name: 'auth.login', summary: 'Authenticate user', tags: ['Auth'])]
    #[Throttle(max: 5, per: 60)]
    public function login(LoginRequest $dto): Response
    {
        $user = $this->users->findByEmail($dto->email);

        if ($user === null || !password_verify($dto->password, $user->password_hash)) {
            return Response::json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        // In production: generate JWT via AuthService
        return Response::json([
            'data' => [
                'message' => 'Login successful',
                'user_id' => $user->id,
            ],
        ]);
    }

    #[Route('POST', '/register', name: 'auth.register', summary: 'Register new user', tags: ['Auth'])]
    #[Throttle(max: 3, per: 60)]
    public function register(CreateUserRequest $dto): Response
    {
        $existing = $this->users->findByEmail($dto->email);

        if ($existing !== null) {
            return Response::json([
                'error'   => 'Validation failed',
                'details' => ['email' => 'Email already registered'],
            ], 422);
        }

        $user = $this->userService->createUser($dto);

        return Response::json([
            'data' => [
                'message' => 'Registration successful',
                'email'   => $user->email,
            ],
        ], 201);
    }

    #[Route('POST', '/logout', name: 'auth.logout', summary: 'Logout user', tags: ['Auth'])]
    #[Authenticated]
    public function logout(ServerRequestInterface $request): Response
    {
        // In production: invalidate JWT, bump token version, etc.
        return Response::json([
            'data' => ['message' => 'Logged out successfully'],
        ]);
    }
}
