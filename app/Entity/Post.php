<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\Id;
use MonkeysLegion\Entity\Attributes\Fillable;
use MonkeysLegion\Entity\Attributes\Hidden;
use MonkeysLegion\Entity\Attributes\Index;
use MonkeysLegion\Entity\Attributes\ManyToOne;
use MonkeysLegion\Entity\Attributes\OneToMany;
use MonkeysLegion\Entity\Attributes\SoftDeletes;
use MonkeysLegion\Entity\Attributes\Timestamps;



/**
 * Blog post entity demonstrating full v2 entity capabilities.
 */
#[Entity(table: 'posts')]
#[SoftDeletes]
#[Timestamps]
#[Index(columns: ['author_id', 'status'], name: 'idx_posts_author_status')]
#[Index(columns: ['slug'], name: 'idx_posts_slug')]
class Post
{
    // ── Fields ──────────────────────────────────────────────────

    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 255)]
    #[Fillable]
    public string $title {
        set(string $value) {
            if (strlen($value) === 0) {
                throw new \InvalidArgumentException('Title cannot be empty');
            }
            $this->title = $value;
        }
    }

    #[Field(type: 'string', length: 300)]
    public string $slug {
        set(string $value) {
            // Auto-generate slug from value
            $slug = strtolower(trim($value));
            $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $slug);
            $this->slug = trim($slug, '-');
        }
    }

    #[Field(type: 'text')]
    #[Fillable]
    public string $body;

    #[Field(type: 'string', length: 50)]
    #[Fillable]
    public string $status = 'draft';

    #[Field(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $published_at = null;

    #[Field(type: 'datetime', nullable: true)]
    #[Hidden]
    public ?\DateTimeImmutable $deleted_at = null;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $updated_at;

    // ── Relationships ──────────────────────────────────────────

    #[ManyToOne(targetEntity: User::class)]
    #[Fillable]
    public User $author;

    /**
     * @var list<Comment>
     */
    #[OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    public array $comments = [];

    // ── Computed Properties ────────────────────────────────────

    public string $excerpt {
        get => mb_strlen($this->body) > 200
            ? mb_substr($this->body, 0, 200) . '…'
            : $this->body;
    }

    public bool $isPublished {
        get => $this->status === 'published' && $this->published_at !== null;
    }

    public int $commentCount {
        get => count($this->comments);
    }

    // ── Business Logic ─────────────────────────────────────────

    public function publish(): void
    {
        $this->status = 'published';
        $this->published_at = new \DateTimeImmutable();
    }

    public function unpublish(): void
    {
        $this->status = 'draft';
        $this->published_at = null;
    }
}
