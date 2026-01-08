<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\ManyToMany;

/**
 * Role entity representing an RBAC role.
 */
#[Entity(table: 'roles')]
class Role
{
    #[Field(type: 'integer')]
    public int $id;

    // Machine name / slug, used internally by HasRolesTrait (e.g. "admin", "editor").
    #[Field(type: 'string', length: 100)]
    public string $slug;

    // Human readable name.
    #[Field(type: 'string', length: 255)]
    public string $name;

    #[Field(type: 'string', length: 255, nullable: true)]
    public ?string $description = null;

    #[Field(type: 'datetime')]
    public \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public \DateTimeImmutable $updated_at;

    /**
     * Inverse side of the many-to-many with User.
     */
    #[ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    public array $users = [];

    // ----------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
