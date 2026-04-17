<?php
declare(strict_types=1);

namespace App\Dto;

use MonkeysLegion\Validation\Attributes\NotBlank;
use MonkeysLegion\Validation\Attributes\Email;
use MonkeysLegion\Validation\Attributes\Length;

/**
 * Request DTO for creating a user.
 */
final readonly class CreateUserRequest
{
    public function __construct(
        #[NotBlank]
        #[Email]
        public string $email,

        #[NotBlank]
        #[Length(min: 2, max: 100)]
        public string $name,

        #[NotBlank]
        #[Length(min: 8, max: 64)]
        public string $password,
    ) {}
}
