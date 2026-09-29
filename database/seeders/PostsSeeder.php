<?php
declare(strict_types=1);

namespace App\Database\Seeders;

use App\Factory\PostFactory;
use MonkeysLegion\Cli\Seeder\Seeder;
use MonkeysLegion\Database\Contracts\ConnectionInterface;

/**
 * PostsSeeder — inserts 20 posts into the posts table.
 *
 * Run with: php bin/ml db:seed PostsSeeder
 */
final class PostsSeeder extends Seeder
{
    protected function seed(): void
    {
        // Get the first user ID as the author.
        $authorId = 1;

        $factory = new PostFactory();

        for ($i = 0; $i < 20; $i++) {
            $post = $factory->make();

            $this->execute(
                'INSERT INTO posts (title, slug, body, status, author_id, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
                [
                    $post->title,
                    $post->slug,
                    $post->body,
                    $post->status,
                    $authorId,
                ],
            );
        }
    }
}
