<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\OneToOne;
use MonkeysLegion\Entity\Attributes\OneToMany;
use MonkeysLegion\Entity\Attributes\ManyToOne;
use MonkeysLegion\Entity\Attributes\ManyToMany;
use MonkeysLegion\Entity\Attributes\JoinTable;

/**
 * User entity representing a user in the system.
 */
#[Entity(table: 'users')]
class User
{
    #[Field(type: 'integer')]
    public int $id;

    #[Field(type: 'string', length: 255)]
    public string $email;

    #[Field(type: 'string', length: 255)]
    public string $password_hash;

    #[Field(type: 'integer', default: 1)]
    public int $token_version = 1;

    #[Field(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $email_verified_at = null;

    #[Field(type: 'string', length: 255, nullable: true)]
    public ?string $two_factor_secret = null;

    #[Field(type: 'json', nullable: true)]
    public ?array $two_factor_recovery_codes = null;

    #[Field(type: 'datetime')]
    public \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public \DateTimeImmutable $updated_at;

    public function __construct()
    {
        // you can prefill defaults if you want:
        // $this->created_at = new \DateTimeImmutable();
        // $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(string $hash): self
    {
        $this->password_hash = $hash;
        return $this;
    }

    public function getTokenVersion(): int
    {
        return $this->token_version;
    }

    public function bumpTokenVersion(): self
    {
        $this->token_version++;
        return $this;
    }

    public function markEmailVerified(?\DateTimeImmutable $at = null): self
    {
        $this->email_verified_at = $at ?? new \DateTimeImmutable();
        return $this;
    }

    public function getTwoFactorSecret(): ?string
    {
        return $this->two_factor_secret;
    }

    public function setTwoFactorSecret(?string $secret): self
    {
        $this->two_factor_secret = $secret;
        return $this;
    }

    public function getTwoFactorRecoveryCodes(): ?array
    {
        return $this->two_factor_recovery_codes;
    }

    public function setTwoFactorRecoveryCodes(?array $codes): self
    {
        $this->two_factor_recovery_codes = $codes;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updated_at;
    }
}
