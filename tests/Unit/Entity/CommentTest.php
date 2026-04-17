<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Comment::class)]
final class CommentTest extends TestCase
{
    #[Test]
    public function bodyIsTrimmed(): void
    {
        $comment = new Comment();
        $comment->body = '  Hello world  ';

        $this->assertSame('Hello world', $comment->body);
    }

    #[Test]
    public function emptyBodyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Comment body cannot be empty');

        $comment = new Comment();
        $comment->body = '   ';
    }

    #[Test]
    public function canSetPostRelationship(): void
    {
        $post = new Post();
        $post->title = 'Test Post';
        $post->body = 'Body';
        $post->slug = 'test-post';

        $comment = new Comment();
        $comment->body = 'Nice post!';
        $comment->post = $post;

        $this->assertSame($post, $comment->post);
    }

    #[Test]
    public function canSetAuthorRelationship(): void
    {
        $user = new User();
        $user->name = 'Jorge';
        $user->email = 'jorge@test.com';

        $comment = new Comment();
        $comment->body = 'Great article!';
        $comment->author = $user;

        $this->assertSame($user, $comment->author);
    }
}
