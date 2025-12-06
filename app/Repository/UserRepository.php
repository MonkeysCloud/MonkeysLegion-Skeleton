<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use MonkeysLegion\Repository\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * DB table for this repository.
     */
    protected string $table = 'users';

    /**
     * Entity class this repository hydrates.
     */
    protected string $entityClass = User::class;
}