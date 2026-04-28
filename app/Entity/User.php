<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\Id;
use MonkeysLegion\Entity\Attributes\Fillable;
use MonkeysLegion\Entity\Attributes\Hidden;
use MonkeysLegion\Entity\Attributes\Index;
use MonkeysLegion\Entity\Attributes\ManyToMany;
use MonkeysLegion\Entity\Attributes\JoinTable;
use MonkeysLegion\Entity\Attributes\Timestamps;

use MonkeysLegion\Auth\Contract\AuthenticatableInterface;
use MonkeysLegion\Auth\Contract\HasRolesInterface;
use MonkeysLegion\Auth\Contract\HasPermissionsInterface;

/**
 * User entity representing a registered user.
 *
 * Uses PHP 8.4 property hooks and asymmetric visibility.
 * Auth traits omitted to avoid property conflicts — interface methods
 * are implemented directly.
 */
#[Entity(table: 'users')]
#[Timestamps]
#[Index(columns: ['email'], name: 'idx_users_email')]
class User implements
    AuthenticatableInterface,
    HasRolesInterface,
    HasPermissionsInterface
{
    // ── Fields ──────────────────────────────────────────────────

    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 255)]
    #[Fillable]
    public string $email {
        set(string $value) {
            $this->email = strtolower(trim($value));
        }
    }

    #[Field(type: 'string', length: 255)]
    #[Fillable]
    public string $name {
        set(string $value) {
            if (strlen($value) === 0) {
                throw new \InvalidArgumentException('Name cannot be empty');
            }
            $this->name = $value;
        }
    }

    #[Field(type: 'string', length: 255)]
    #[Hidden]
    public string $password_hash;

    #[Field(type: 'integer', default: 1)]
    public int $token_version = 1;

    #[Field(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $email_verified_at = null;

    #[Field(type: 'string', length: 255, nullable: true)]
    #[Hidden]
    public ?string $two_factor_secret = null;

    #[Field(type: 'json', nullable: true)]
    #[Hidden]
    public ?array $two_factor_recovery_codes = null;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $updated_at;

    // ── RBAC ───────────────────────────────────────────────────

    /** @var list<string> */
    protected array $roles = [];

    /** @var list<string> */
    protected array $permissions = [];

    protected ?string $rememberToken = null;

    // ── Relationships ──────────────────────────────────────────

    /**
     * @var list<Role>
     */
    #[ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[JoinTable(
        name: 'user_roles',
        joinColumn: 'user_id',
        inverseColumn: 'role_id',
    )]
    public array $roleEntities = [];

    // ── Computed Properties ────────────────────────────────────

    public string $displayName {
        get => "{$this->name} <{$this->email}>";
    }

    public bool $isVerified {
        get => $this->email_verified_at !== null;
    }

    public bool $hasTwoFactor {
        get => $this->two_factor_secret !== null;
    }

    // ── Business Logic ─────────────────────────────────────────

    public function markEmailVerified(?\DateTimeImmutable $at = null): void
    {
        $this->email_verified_at = $at ?? new \DateTimeImmutable();
    }

    public function bumpTokenVersion(): void
    {
        $this->token_version++;
    }

    // ── AuthenticatableInterface ───────────────────────────────

    public function getAuthIdentifier(): int|string
    {
        return $this->id;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getTokenVersion(): int
    {
        return $this->token_version;
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function setRememberToken(?string $token): void
    {
        $this->rememberToken = $token;
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->hasTwoFactor;
    }

    // ── HasRolesInterface ──────────────────────────────────────

    /** @return list<string> */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    /** @param list<string> $roles */
    public function hasAnyRole(array $roles): bool
    {
        return array_intersect($roles, $this->roles) !== [];
    }

    /** @param list<string> $roles */
    public function hasAllRoles(array $roles): bool
    {
        return array_diff($roles, $this->roles) === [];
    }

    // ── HasPermissionsInterface ────────────────────────────────

    /** @return list<string> */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        if (in_array($permission, $this->permissions, true)) {
            return true;
        }
        foreach ($this->permissions as $p) {
            if (str_ends_with($p, '.*')) {
                $prefix = substr($p, 0, -1);
                if (str_starts_with($permission, $prefix)) {
                    return true;
                }
            }
        }
        return in_array('*', $this->permissions, true);
    }

    /** @param list<string> $permissions */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $p) {
            if ($this->hasPermission($p)) {
                return true;
            }
        }
        return false;
    }

    /** @param list<string> $permissions */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $p) {
            if (!$this->hasPermission($p)) {
                return false;
            }
        }
        return true;
    }
}
