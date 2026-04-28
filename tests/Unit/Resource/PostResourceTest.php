<?php
declare(strict_types=1);

namespace Tests\Unit\Resource;

use App\Entity\Post;
use App\Entity\User;
use App\Resource\PostResource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PostResource::class)]
final class PostResourceTest extends TestCase
{
    private function makePost(): Post
    {
        $author = new User();
        $author->email = 'author@test.com';
        $author->name = 'Author';
        $author->password_hash = 'hash';
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 5);

        $post = new Post();
        $post->title = 'Test Post';
        $post->slug = 'test-post';
        $post->body = 'This is a test body for the post.';
        $post->status = 'draft';
        $post->author = $author;

        $ref = new \ReflectionProperty($post, 'id');
        $ref->setValue($post, 10);
        $ref = new \ReflectionProperty($post, 'created_at');
        $ref->setValue($post, new \DateTimeImmutable('2026-01-01'));
        $ref = new \ReflectionProperty($post, 'updated_at');
        $ref->setValue($post, new \DateTimeImmutable('2026-01-02'));

        return $post;
    }

    #[Test]
    public function toArrayReturnsCorrectStructure(): void
    {
        $post = $this->makePost();
        $data = PostResource::toArray($post);

        $this->assertSame(10, $data['id']);
        $this->assertSame('posts', $data['type']);
        $this->assertSame('Test Post', $data['attributes']['title']);
        $this->assertSame('test-post', $data['attributes']['slug']);
        $this->assertFalse($data['attributes']['is_published']);
        $this->assertNull($data['attributes']['published_at']);
        $this->assertArrayHasKey('excerpt', $data['attributes']);
        $this->assertArrayHasKey('relationships', $data);
        $this->assertSame(5, $data['relationships']['author']['id']);
        $this->assertSame('users', $data['relationships']['author']['type']);
    }

    #[Test]
    public function toArrayWithPublishedPost(): void
    {
        $post = $this->makePost();
        $post->publish();
        $data = PostResource::toArray($post);

        $this->assertTrue($data['attributes']['is_published']);
        $this->assertNotNull($data['attributes']['published_at']);
    }

    #[Test]
    public function makeReturnsJsonResponse(): void
    {
        $response = PostResource::make($this->makePost());

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('data', $body);
    }

    #[Test]
    public function collectionReturnsCorrectFormat(): void
    {
        $posts = [$this->makePost(), $this->makePost()];
        $response = PostResource::collection($posts);

        $body = json_decode((string) $response->getBody(), true);
        $this->assertCount(2, $body['data']);
        $this->assertSame(2, $body['meta']['total']);
    }
}
