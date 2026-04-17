<?php
declare(strict_types=1);

namespace App\Resource;

use App\Entity\Post;
use MonkeysLegion\Http\Message\Response;

/**
 * JSON:API resource for Post entity.
 */
final class PostResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Post $post): array
    {
        return [
            'id'         => $post->id,
            'type'       => 'posts',
            'attributes' => [
                'title'        => $post->title,
                'slug'         => $post->slug,
                'excerpt'      => $post->excerpt,
                'body'         => $post->body,
                'status'       => $post->status,
                'is_published' => $post->isPublished,
                'published_at' => $post->published_at?->format('c'),
                'created_at'   => $post->created_at->format('c'),
                'updated_at'   => $post->updated_at->format('c'),
            ],
            'relationships' => [
                'author' => [
                    'id'   => $post->author->id,
                    'type' => 'users',
                    'name' => $post->author->name,
                ],
            ],
        ];
    }

    public static function make(Post $post, int $status = 200): Response
    {
        return Response::json(['data' => self::toArray($post)], $status);
    }

    /**
     * @param list<Post> $posts
     */
    public static function collection(array $posts, int $status = 200): Response
    {
        $data = array_map(self::toArray(...), $posts);

        return Response::json([
            'data'  => $data,
            'meta'  => ['total' => count($data)],
        ], $status);
    }
}
