<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\Api\PostController;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Service\PostService;
use Psr\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

#[CoversClass(PostController::class)]
final class PostControllerTest extends TestCase
{
    private function makePost(int $id = 1): Post
    {
        $author = new User();
        $author->email = 'author@test.com';
        $author->name = 'Author';
        $author->password_hash = 'hash';
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);

        $post = new Post();
        $post->title = "Post {$id}";
        $post->slug = "post-{$id}";
        $post->body = 'Body content';
        $post->status = 'published';
        $post->author = $author;

        $ref = new \ReflectionProperty($post, 'id');
        $ref->setValue($post, $id);
        $ref = new \ReflectionProperty($post, 'created_at');
        $ref->setValue($post, new \DateTimeImmutable());
        $ref = new \ReflectionProperty($post, 'updated_at');
        $ref->setValue($post, new \DateTimeImmutable());

        return $post;
    }

    private function makeController(?PostRepository $repo = null): PostController
    {
        $repo = $repo ?? $this->createMock(PostRepository::class);
        $service = new PostService(
            $repo,
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LoggerInterface::class),
        );

        return new PostController($service, $repo);
    }

    private function mockRequest(array $queryParams = []): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($queryParams);
        return $request;
    }

    #[Test]
    public function indexReturnsList(): void
    {
        $repo = $this->createMock(PostRepository::class);
        $repo->method('findPublished')->willReturn([$this->makePost(1), $this->makePost(2)]);

        $controller = $this->makeController($repo);
        $response = $controller->index($this->mockRequest());

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertCount(2, $body['data']);
    }

    #[Test]
    public function indexWithSearchQuery(): void
    {
        $repo = $this->createMock(PostRepository::class);
        $repo->method('search')->with('hello')->willReturn([$this->makePost(3)]);

        $controller = $this->makeController($repo);
        $response = $controller->index($this->mockRequest(['q' => 'hello']));

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertCount(1, $body['data']);
    }

    #[Test]
    public function showReturnsPost(): void
    {
        $post = $this->makePost(42);
        $repo = $this->createMock(PostRepository::class);
        $repo->method('findOrFail')->with(42)->willReturn($post);

        $controller = $this->makeController($repo);
        $response = $controller->show($this->mockRequest(), '42');

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function publishCallsService(): void
    {
        $post = $this->makePost(10);
        $post->publish();

        $repo = $this->createMock(PostRepository::class);
        $repo->method('findOrFail')->with(10)->willReturn($post);

        $controller = $this->makeController($repo);
        $response = $controller->publish('10');

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function destroyReturns204(): void
    {
        $repo = $this->createMock(PostRepository::class);
        $repo->expects($this->once())->method('delete')->with(10);

        $controller = $this->makeController($repo);
        $response = $controller->destroy('10');

        $this->assertSame(204, $response->getStatusCode());
    }
}
