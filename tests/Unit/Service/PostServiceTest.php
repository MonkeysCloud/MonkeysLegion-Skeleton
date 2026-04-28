<?php
declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Dto\CreatePostRequest;
use App\Entity\Post;
use App\Entity\User;
use App\Event\PostPublished;
use App\Repository\PostRepository;
use App\Service\PostService;
use Psr\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(PostService::class)]
final class PostServiceTest extends TestCase
{
    private function makeService(
        ?PostRepository $repo = null,
        ?EventDispatcherInterface $events = null,
        ?LoggerInterface $logger = null,
    ): PostService {
        return new PostService(
            $repo ?? $this->createMock(PostRepository::class),
            $events ?? $this->createMock(EventDispatcherInterface::class),
            $logger ?? $this->createMock(LoggerInterface::class),
        );
    }

    #[Test]
    public function createPostSetsSlugFromTitle(): void
    {
        $author = new User();
        $author->name = 'Author';
        $author->email = 'author@test.com';

        $repo = $this->createMock(PostRepository::class);
        $repo->expects($this->once())->method('persist');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $service = $this->makeService(repo: $repo, logger: $logger);

        $dto = new CreatePostRequest(title: 'Hello World', body: 'Content here');

        $post = $service->createPost($dto, $author);

        $this->assertSame('Hello World', $post->title);
        $this->assertSame('hello-world', $post->slug);
        $this->assertSame('draft', $post->status);
    }

    #[Test]
    public function publishDispatchesPostPublishedEvent(): void
    {
        $post = new Post();
        $post->title = 'Test';
        $post->body = 'Body';
        $post->slug = 'test';
        $post->status = 'draft';

        $repo = $this->createMock(PostRepository::class);
        $repo->method('findOrFail')->with(1)->willReturn($post);
        $repo->expects($this->once())->method('persist');

        $events = $this->createMock(EventDispatcherInterface::class);
        $events->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PostPublished::class));

        $service = $this->makeService(repo: $repo, events: $events);
        $result = $service->publish(1);

        $this->assertTrue($result->isPublished);
    }
}
