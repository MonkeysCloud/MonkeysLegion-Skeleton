<?php
declare(strict_types=1);

namespace App\Dto;

use MonkeysLegion\Validation\Attributes\Email;
use MonkeysLegion\Validation\Attributes\Length;

/**
 * Request DTO for updating an existing user.
 */
final readonly class UpdateUserRequest
{
    public function __construct(
        #[Email]
        public ?string $email = null,

        #[Length(min: 2, max: 100)]
        public ?string $name = null,

        #[Length(min: 8, max: 64)]
        public ?string $password = null,
    ) {}
}
