<?php
declare(strict_types=1);

namespace App\Factory;

use App\Entity\Post;
use MonkeysLegion\Cli\Factory\ModelFactory;
use MonkeysLegion\Cli\Factory\FakerProvider;

/**
 * PostFactory — test data factory for the Post entity.
 *
 * Usage:
 *   $post = (new PostFactory())->make();
 *   $published = (new PostFactory())->published()->make();
 *   $posts = (new PostFactory())->count(20)->make();
 */
final class PostFactory extends ModelFactory
{
    protected string $entityClass = Post::class;

    public function definition(): array
    {
        $title = FakerProvider::paragraph(1);

        return [
            'title'  => rtrim($title, '.'),
            'slug'   => rtrim($title, '.'),
            'body'   => FakerProvider::paragraph(5),
            'status' => 'draft',
        ];
    }

    /**
     * State: published post.
     */
    public function published(): static
    {
        return $this->state('published', [
            'status'       => 'published',
            'published_at' => new \DateTimeImmutable(),
        ])->apply('published');
    }
}
