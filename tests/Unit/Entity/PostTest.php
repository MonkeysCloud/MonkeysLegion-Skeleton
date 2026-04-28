<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Post entity — property hooks, computed properties, business logic.
 */
#[CoversClass(Post::class)]
final class PostTest extends TestCase
{
    #[Test]
    public function slugIsGeneratedFromTitle(): void
    {
        $post = new Post();
        $post->slug = 'Hello World — A Great Post!';

        $this->assertSame('hello-world-a-great-post', $post->slug);
    }

    #[Test]
    public function titleCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $post = new Post();
        $post->title = '';
    }

    #[Test]
    public function excerptIsTruncatedAt200Chars(): void
    {
        $post = new Post();
        $post->body = str_repeat('x', 250);

        $this->assertSame(mb_substr(str_repeat('x', 250), 0, 200) . '…', $post->excerpt);

        $post->body = 'Short body';

        $this->assertSame('Short body', $post->excerpt);
    }

    #[Test]
    public function publishSetsStatusAndTimestamp(): void
    {
        $post = new Post();
        $post->title = 'Draft';
        $post->body = 'Content';
        $post->status = 'draft';

        $this->assertFalse($post->isPublished);

        $post->publish();

        $this->assertSame('published', $post->status);
        $this->assertInstanceOf(\DateTimeImmutable::class, $post->published_at);
        $this->assertTrue($post->isPublished);
    }

    #[Test]
    public function unpublishRevertsToAndClearsDate(): void
    {
        $post = new Post();
        $post->title = 'Test';
        $post->body = 'Content';
        $post->publish();
        $post->unpublish();

        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);
        $this->assertFalse($post->isPublished);
    }

    #[Test]
    public function commentCountReflectsCommentsArray(): void
    {
        $post = new Post();
        $post->title = 'Test';
        $post->body = 'Content';

        $this->assertSame(0, $post->commentCount);
    }
}
