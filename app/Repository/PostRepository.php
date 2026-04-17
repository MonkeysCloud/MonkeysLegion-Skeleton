<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use MonkeysLegion\Query\Repository\EntityRepository;

/**
 * @extends EntityRepository<Post>
 */
class PostRepository extends EntityRepository
{
    protected string $table = 'posts';
    protected string $entityClass = Post::class;

    /**
     * @return list<Post>
     */
    public function findPublished(): array
    {
        $rows = $this->query()
            ->where('status', '=', 'published')
            ->whereNull('deleted_at')
            ->orderBy('published_at', 'DESC')
            ->get();

        /** @var list<Post> */
        return array_map(
            fn(array $row) => $this->findOrFail($row['id']),
            $rows,
        );
    }

    /**
     * @return list<Post>
     */
    public function findByAuthor(int $authorId): array
    {
        /** @var list<Post> */
        return $this->findBy(
            criteria: ['author_id' => $authorId],
            orderBy: ['created_at' => 'DESC'],
        );
    }

    /**
     * @return list<Post>
     */
    public function search(string $term): array
    {
        $rows = $this->query()
            ->where('status', '=', 'published')
            ->whereNull('deleted_at')
            ->where('title', 'LIKE', "%{$term}%")
            ->orderBy('published_at', 'DESC')
            ->get();

        /** @var list<Post> */
        return array_map(
            fn(array $row) => $this->findOrFail($row['id']),
            $rows,
        );
    }
}
