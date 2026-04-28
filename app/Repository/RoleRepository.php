<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Role;
use MonkeysLegion\Query\Repository\EntityRepository;

/**
 * @extends EntityRepository<Role>
 */
class RoleRepository extends EntityRepository
{
    protected string $table = 'roles';
    protected string $entityClass = Role::class;

    public function findBySlug(string $slug): ?Role
    {
        /** @var Role|null */
        return $this->findOneBy(['slug' => strtolower($slug)]);
    }
}
