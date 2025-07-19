<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use MonkeysLegion\Repository\EntityRepository;

class UserRepository extends EntityRepository
{
    protected string $table       = 'user';
    protected string $entityClass = User::class;
}