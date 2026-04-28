<?php
declare(strict_types=1);

namespace Tests\Unit\Event;

use App\Entity\Post;
use App\Entity\User;
use App\Event\PostPublished;
use App\Event\UserCreated;
use App\Event\UserUpdated;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserCreated::class)]
#[CoversClass(UserUpdated::class)]
#[CoversClass(PostPublished::class)]
final class EventTest extends TestCase
{
    #[Test]
    public function userCreatedHoldsUserAndTimestamp(): void
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';

        $event = new UserCreated($user);

        $this->assertSame($user, $event->user);
        $this->assertInstanceOf(\DateTimeImmutable::class, $event->createdAt);
    }

    #[Test]
    public function userCreatedAcceptsCustomTimestamp(): void
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';
        $ts = new \DateTimeImmutable('2026-01-01');

        $event = new UserCreated($user, $ts);

        $this->assertSame($ts, $event->createdAt);
    }

    #[Test]
    public function userUpdatedHoldsUserAndTimestamp(): void
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';

        $event = new UserUpdated($user);

        $this->assertSame($user, $event->user);
        $this->assertInstanceOf(\DateTimeImmutable::class, $event->updatedAt);
    }

    #[Test]
    public function postPublishedHoldsPostAndTimestamp(): void
    {
        $post = new Post();
        $post->title = 'Test';
        $post->body = 'Body';
        $post->slug = 'test';

        $event = new PostPublished($post);

        $this->assertSame($post, $event->post);
        $this->assertInstanceOf(\DateTimeImmutable::class, $event->publishedAt);
    }

    #[Test]
    public function eventsAreReadonly(): void
    {
        $this->assertTrue((new \ReflectionClass(UserCreated::class))->isReadOnly());
        $this->assertTrue((new \ReflectionClass(UserUpdated::class))->isReadOnly());
        $this->assertTrue((new \ReflectionClass(PostPublished::class))->isReadOnly());
    }
}
