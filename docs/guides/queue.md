# Queue & Jobs

MonKeysLegion provides background job processing with Redis, database, and null drivers.

## Creating Jobs

```php
<?php
declare(strict_types=1);

namespace App\Job;

use MonkeysLegion\Queue\Job;

final class SendWelcomeEmailJob extends Job
{
    public function __construct(
        public readonly int $userId,
    ) {}

    public function handle(UserService $users, MailerInterface $mail): void
    {
        $user = $users->find($this->userId);
        $mail->send($user->email, 'Welcome!', 'emails.welcome');
    }
}
```

## Dispatching Jobs

```php
// Dispatch to default queue
SendWelcomeEmailJob::dispatch($user->id);

// Dispatch to a specific queue
SendWelcomeEmailJob::dispatch($user->id)->onQueue('emails');

// Delay execution
SendWelcomeEmailJob::dispatch($user->id)->delay(60); // 60 seconds

// Dispatch synchronously (for testing)
SendWelcomeEmailJob::dispatchSync($user->id);
```

## Job Chaining

```php
ProcessFileJob::dispatch($fileId)
    ->chain([
        new GenerateThumbnailJob($fileId),
        new NotifyUserJob($userId),
    ]);
```

## Running Workers

```bash
# Start a queue worker
php bin/ml queue:work

# Process a single job
php bin/ml queue:work --once

# Specify queue and attempts
php bin/ml queue:work --queue=default,emails --tries=3
```

## Failed Jobs

```bash
# List failed jobs
php bin/ml queue:failed

# Retry a failed job
php bin/ml queue:retry <job-id>

# Retry all failed jobs
php bin/ml queue:retry all

# Flush failed jobs
php bin/ml queue:flush
```

## Configuration

```hocon
# config/queue.mlc
queue {
    default = ${QUEUE_DRIVER:redis}

    connections {
        redis {
            driver   = "redis"
            queue    = "default"
            retry_after = 90
        }
        database {
            driver   = "database"
            table    = "jobs"
            retry_after = 90
        }
    }
}
```

## Queue Dashboard

Access the queue dashboard for monitoring jobs, retries, and failures:

```bash
php bin/ml queue:dashboard
```

## Testing with Queue Fake

```php
use MonkeysLegion\Testing\Fakes\QueueFake;

public function test_welcome_email_queued(): void
{
    QueueFake::fake();

    $this->service->registerUser(['email' => 'test@example.com']);

    QueueFake::assertPushed(SendWelcomeEmailJob::class);
}
```
