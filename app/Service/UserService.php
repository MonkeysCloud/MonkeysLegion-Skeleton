<?php
declare(strict_types=1);

namespace App\Service;

use MonkeysLegion\DI\Attributes\Singleton;
use Psr\EventDispatcher\EventDispatcherInterface;

use Psr\Log\LoggerInterface;

use App\Dto\CreateUserRequest;
use App\Dto\UpdateUserRequest;
use App\Entity\User;
use App\Event\UserCreated;
use App\Event\UserUpdated;
use App\Repository\UserRepository;

/**
 * Business logic for user management.
 */
#[Singleton]
final class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EventDispatcherInterface $events,
        private readonly LoggerInterface $logger,
    ) {}

    public function createUser(CreateUserRequest $dto): User
    {
        $user = new User();
        $user->email = $dto->email;
        $user->name = $dto->name;
        $user->password_hash = password_hash($dto->password, PASSWORD_DEFAULT);

        $this->users->persist($user);

        $this->events->dispatch(new UserCreated($user));
        $this->logger->info('User created', ['email' => $user->email]);

        return $user;
    }

    public function findUser(int $id): ?User
    {
        return $this->users->find($id);
    }

    public function updateUser(int $id, UpdateUserRequest $dto): User
    {
        $user = $this->users->findOrFail($id);

        if ($dto->email !== null) {
            $user->email = $dto->email;
        }

        if ($dto->name !== null) {
            $user->name = $dto->name;
        }

        if ($dto->password !== null) {
            $user->password_hash = password_hash($dto->password, PASSWORD_DEFAULT);
        }

        $this->users->persist($user);

        $this->events->dispatch(new UserUpdated($user));
        $this->logger->info('User updated', ['email' => $user->email]);

        return $user;
    }

    public function deleteUser(int $id): void
    {
        $this->users->delete($id);
        $this->logger->info('User deleted', ['id' => $id]);
    }
}
