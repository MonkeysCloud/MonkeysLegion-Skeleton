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
        /** @var list<Post> */
        return $this->findBy(
            criteria: ['status' => 'published', 'deleted_at' => null],
            orderBy: ['published_at' => 'DESC'],
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
        // Use query() for LIKE, then batch-load via findByIds() (2 queries, not N+1)
        $ids = array_column(
            $this->query()
                ->where('status', '=', 'published')
                ->whereNull('deleted_at')
                ->where('title', 'LIKE', "%{$term}%")
                ->orderBy('published_at', 'DESC')
                ->get(),
            'id',
        );

        /** @var list<Post> */
        return $this->findByIds($ids);
    }
}
