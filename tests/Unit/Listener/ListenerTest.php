<?php
declare(strict_types=1);

namespace Tests\Unit\Listener;

use App\Entity\Post;
use App\Entity\User;
use App\Event\PostPublished;
use App\Event\UserCreated;
use App\Listener\NotifyAdminOnPost;
use App\Listener\SendWelcomeEmail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(SendWelcomeEmail::class)]
#[CoversClass(NotifyAdminOnPost::class)]
final class ListenerTest extends TestCase
{
    #[Test]
    public function sendWelcomeEmailLogsOnInvoke(): void
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';
        $user->password_hash = 'hash';

        $ref = new \ReflectionProperty($user, 'id');
        $ref->setValue($user, 42);

        $event = new UserCreated($user);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('Queuing welcome email', $this->callback(
                fn(array $ctx) => $ctx['user_id'] === 42 && $ctx['email'] === 'test@test.com'
            ));

        $listener = new SendWelcomeEmail($logger);
        $listener($event);
    }

    #[Test]
    public function notifyAdminOnPostLogsOnInvoke(): void
    {
        $post = new Post();
        $post->title = 'New Article';
        $post->body = 'Content';
        $post->slug = 'new-article';

        $ref = new \ReflectionProperty($post, 'id');
        $ref->setValue($post, 7);

        $event = new PostPublished($post);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('Post published — notifying admin', $this->callback(
                fn(array $ctx) => $ctx['post_id'] === 7 && $ctx['title'] === 'New Article'
            ));

        $listener = new NotifyAdminOnPost($logger);
        $listener($event);
    }

    #[Test]
    public function sendWelcomeEmailHasListenerAttribute(): void
    {
        $ref = new \ReflectionClass(SendWelcomeEmail::class);
        $attrs = $ref->getAttributes();
        $names = array_map(fn(\ReflectionAttribute $a) => $a->getName(), $attrs);

        $this->assertContains('MonkeysLegion\Events\Attribute\Listener', $names);
    }

    #[Test]
    public function notifyAdminOnPostHasListenerAttribute(): void
    {
        $ref = new \ReflectionClass(NotifyAdminOnPost::class);
        $attrs = $ref->getAttributes();
        $names = array_map(fn(\ReflectionAttribute $a) => $a->getName(), $attrs);

        $this->assertContains('MonkeysLegion\Events\Attribute\Listener', $names);
    }
}
