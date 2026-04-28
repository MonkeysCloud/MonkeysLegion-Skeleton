<?php
declare(strict_types=1);

namespace App\Listener;

use MonkeysLegion\Events\Attribute\Listener;

use Psr\Log\LoggerInterface;

use App\Event\PostPublished;

/**
 * Notifies admin when a post is published.
 */
#[Listener(PostPublished::class)]
final class NotifyAdminOnPost
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(PostPublished $event): void
    {
        $this->logger->info('Post published — notifying admin', [
            'post_id' => $event->post->id,
            'title'   => $event->post->title,
        ]);
    }
}
