<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\Post;

/**
 * Dispatched when a post is published.
 */
final readonly class PostPublished
{
    public function __construct(
        public Post $post,
        public \DateTimeImmutable $publishedAt = new \DateTimeImmutable(),
    ) {}
}
