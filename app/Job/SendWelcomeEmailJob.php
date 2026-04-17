<?php
declare(strict_types=1);

namespace App\Job;

use MonkeysLegion\Queue\Contracts\ShouldQueue;

use Psr\Log\LoggerInterface;

use App\Repository\UserRepository;

/**
 * Queue job to send a welcome email to a new user.
 */
final class SendWelcomeEmailJob implements ShouldQueue
{
    public function __construct(
        private readonly int $userId,
    ) {}

    public function handle(
        UserRepository $users,
        LoggerInterface $logger,
    ): void {
        $user = $users->find($this->userId);

        if ($user === null) {
            $logger->warning('SendWelcomeEmail: user not found', ['user_id' => $this->userId]);
            return;
        }

        // In production: use Mailer to send the actual email
        $logger->info('Welcome email sent', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        // Handle permanent failure — log, notify admin, etc.
    }
}
