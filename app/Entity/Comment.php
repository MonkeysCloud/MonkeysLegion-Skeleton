<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\Id;
use MonkeysLegion\Entity\Attributes\Fillable;
use MonkeysLegion\Entity\Attributes\ManyToOne;
use MonkeysLegion\Entity\Attributes\Timestamps;

/**
 * Comment on a blog post.
 */
#[Entity(table: 'comments')]
#[Timestamps]
class Comment
{
    // ── Fields ──────────────────────────────────────────────────

    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'text')]
    #[Fillable]
    public string $body {
        set(string $value) {
            if (strlen(trim($value)) === 0) {
                throw new \InvalidArgumentException('Comment body cannot be empty');
            }
            $this->body = trim($value);
        }
    }

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $updated_at;

    // ── Relationships ──────────────────────────────────────────

    #[ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    #[Fillable]
    public Post $post;

    #[ManyToOne(targetEntity: User::class)]
    #[Fillable]
    public User $author;
}
