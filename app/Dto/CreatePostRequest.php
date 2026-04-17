<?php
declare(strict_types=1);

namespace App\Dto;

use MonkeysLegion\Validation\Attributes\NotBlank;
use MonkeysLegion\Validation\Attributes\Length;

/**
 * Request DTO for creating a blog post.
 */
final readonly class CreatePostRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(min: 3, max: 255)]
        public string $title,

        #[NotBlank]
        public string $body,

        public string $status = 'draft',
    ) {}
}
