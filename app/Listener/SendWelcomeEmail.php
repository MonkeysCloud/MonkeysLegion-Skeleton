<?php
declare(strict_types=1);

namespace App\Listener;

use MonkeysLegion\Events\Attribute\Listener;

use Psr\Log\LoggerInterface;

use App\Event\UserCreated;
use App\Job\SendWelcomeEmailJob;

/**
 * Dispatches a welcome email job when a new user is created.
 */
#[Listener(UserCreated::class)]
final class SendWelcomeEmail
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(UserCreated $event): void
    {
        $this->logger->info('Queuing welcome email', [
            'user_id' => $event->user->id,
            'email'   => $event->user->email,
        ]);

        // In production, this would dispatch to the queue:
        // dispatch(new SendWelcomeEmailJob($event->user->id));
    }
}
