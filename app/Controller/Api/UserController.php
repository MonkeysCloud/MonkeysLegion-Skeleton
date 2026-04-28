<?php
declare(strict_types=1);

namespace App\Controller\Api;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Auth\Attribute\RequiresRole;
use MonkeysLegion\Http\Message\Response;

use Psr\Http\Message\ServerRequestInterface;

use App\Dto\CreateUserRequest;
use App\Dto\UpdateUserRequest;
use App\Resource\UserResource;
use App\Service\UserService;
use App\Repository\UserRepository;

/**
 * User management API.
 */
#[RoutePrefix('/api/v2/users')]
#[Middleware(['cors', 'throttle:60,1'])]
#[Authenticated]
final class UserController
{
    public function __construct(
        private readonly UserService $service,
        private readonly UserRepository $users,
    ) {}

    #[Route('GET', '/', name: 'users.index', summary: 'List all users', tags: ['Users'])]
    public function index(ServerRequestInterface $request): Response
    {
        $users = $this->users->findActiveUsers();

        return UserResource::collection($users);
    }

    #[Route('GET', '/{id:\d+}', name: 'users.show', summary: 'Get a user by ID', tags: ['Users'])]
    public function show(ServerRequestInterface $request, string $id): Response
    {
        $user = $this->users->findOrFail((int) $id);

        return UserResource::make($user);
    }

    #[Route('POST', '/', name: 'users.create', summary: 'Create a new user', tags: ['Users'])]
    #[RequiresRole('admin')]
    public function create(CreateUserRequest $dto): Response
    {
        $user = $this->service->createUser($dto);

        return UserResource::make($user, 201);
    }

    #[Route('PUT', '/{id:\d+}', name: 'users.update', summary: 'Update a user', tags: ['Users'])]
    #[RequiresRole('admin')]
    public function update(UpdateUserRequest $dto, string $id): Response
    {
        $user = $this->service->updateUser((int) $id, $dto);

        return UserResource::make($user);
    }

    #[Route('DELETE', '/{id:\d+}', name: 'users.destroy', summary: 'Delete a user', tags: ['Users'])]
    #[RequiresRole('admin')]
    public function destroy(string $id): Response
    {
        $this->service->deleteUser((int) $id);

        return Response::noContent();
    }
}
