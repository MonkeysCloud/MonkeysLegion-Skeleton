<?php
declare(strict_types=1);

namespace App\Service;

use MonkeysLegion\DI\Attributes\Singleton;
use Psr\EventDispatcher\EventDispatcherInterface;

use Psr\Log\LoggerInterface;

use App\Dto\CreatePostRequest;
use App\Entity\Post;
use App\Entity\User;
use App\Event\PostPublished;
use App\Repository\PostRepository;

/**
 * Business logic for blog post management.
 */
#[Singleton]
final class PostService
{
    public function __construct(
        private readonly PostRepository $posts,
        private readonly EventDispatcherInterface $events,
        private readonly LoggerInterface $logger,
    ) {}

    public function createPost(CreatePostRequest $dto, User $author): Post
    {
        $post = new Post();
        $post->title = $dto->title;
        $post->slug = $dto->title;
        $post->body = $dto->body;
        $post->status = $dto->status;
        $post->author = $author;

        $this->posts->persist($post);
        $this->logger->info('Post created', ['title' => $post->title]);

        return $post;
    }

    public function publish(int $id): Post
    {
        $post = $this->posts->findOrFail($id);
        $post->publish();

        $this->posts->persist($post);

        $this->events->dispatch(new PostPublished($post));
        $this->logger->info('Post published', ['title' => $post->title]);

        return $post;
    }

    public function findPost(int $id): ?Post
    {
        return $this->posts->find($id);
    }

    public function deletePost(int $id): void
    {
        $this->posts->delete($id);
        $this->logger->info('Post deleted', ['id' => $id]);
    }
}
