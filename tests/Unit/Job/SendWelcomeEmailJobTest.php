<?php
declare(strict_types=1);

namespace Tests\Unit\Job;

use App\Entity\User;
use App\Job\SendWelcomeEmailJob;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(SendWelcomeEmailJob::class)]
final class SendWelcomeEmailJobTest extends TestCase
{
    #[Test]
    public function handleLogsWhenUserFound(): void
    {
        $user = new User();
        $user->email = 'welcome@test.com';
        $user->name = 'Welcome';
        $user->password_hash = 'hash';

        $ref = new \ReflectionProperty($user, 'id');
        $ref->setValue($user, 10);

        $repo = $this->createMock(UserRepository::class);
        $repo->method('find')->with(10)->willReturn($user);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('Welcome email sent', $this->callback(
                fn(array $ctx) => $ctx['user_id'] === 10 && $ctx['email'] === 'welcome@test.com'
            ));

        $job = new SendWelcomeEmailJob(10);
        $job->handle($repo, $logger);
    }

    #[Test]
    public function handleWarnsWhenUserNotFound(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('find')->with(999)->willReturn(null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('warning')
            ->with('SendWelcomeEmail: user not found', ['user_id' => 999]);

        $job = new SendWelcomeEmailJob(999);
        $job->handle($repo, $logger);
    }

    #[Test]
    public function failedMethodDoesNotThrow(): void
    {
        $job = new SendWelcomeEmailJob(1);
        $job->failed(new \RuntimeException('test'));

        $this->assertTrue(true); // No exception thrown
    }

    #[Test]
    public function implementsShouldQueue(): void
    {
        $ref = new \ReflectionClass(SendWelcomeEmailJob::class);
        $interfaces = array_map(
            fn(\ReflectionClass $i) => $i->getName(),
            $ref->getInterfaces(),
        );

        $this->assertContains('MonkeysLegion\Queue\Contracts\ShouldQueue', $interfaces);
    }
}
