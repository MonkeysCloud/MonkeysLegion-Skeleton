<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\Id;
use MonkeysLegion\Entity\Attributes\Fillable;
use MonkeysLegion\Entity\Attributes\ManyToMany;
use MonkeysLegion\Entity\Attributes\Timestamps;

/**
 * Role entity representing an RBAC role.
 */
#[Entity(table: 'roles')]
#[Timestamps]
class Role
{
    // ── Fields ──────────────────────────────────────────────────

    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 100)]
    #[Fillable]
    public string $slug {
        set(string $value) {
            $this->slug = strtolower(trim($value));
        }
    }

    #[Field(type: 'string', length: 255)]
    #[Fillable]
    public string $name;

    #[Field(type: 'string', length: 255, nullable: true)]
    #[Fillable]
    public ?string $description = null;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $updated_at;

    // ── Relationships ──────────────────────────────────────────

    /**
     * @var list<User>
     */
    #[ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    public array $users = [];
}
