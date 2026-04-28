<?php
declare(strict_types=1);

namespace App\Dto;

use MonkeysLegion\Validation\Attributes\NotBlank;
use MonkeysLegion\Validation\Attributes\Email;
use MonkeysLegion\Validation\Attributes\Length;

/**
 * Request DTO for authentication.
 */
final readonly class LoginRequest
{
    public function __construct(
        #[NotBlank]
        #[Email]
        public string $email,

        #[NotBlank]
        #[Length(min: 8, max: 64)]
        public string $password,
    ) {}
}
