<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use MonkeysLegion\Query\Repository\EntityRepository;

/**
 * @extends EntityRepository<User>
 */
class UserRepository extends EntityRepository
{
    protected string $table = 'users';
    protected string $entityClass = User::class;

    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return $this->findOneBy(['email' => strtolower(trim($email))]);
    }

    /**
     * @return list<User>
     */
    public function findActiveUsers(): array
    {
        /** @var list<User> */
        return $this->findBy(
            criteria: [],
            orderBy: ['created_at' => 'DESC'],
        );
    }
}