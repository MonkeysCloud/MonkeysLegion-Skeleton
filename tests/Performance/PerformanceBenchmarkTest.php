<?php
declare(strict_types=1);

namespace Tests\Performance;

use App\Dto\CreateUserRequest;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Resource\PostResource;
use App\Resource\UserResource;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Performance benchmarks.
 *
 * Run with: phpunit --testsuite=Performance
 * Each test asserts that operations complete within a time budget.
 */
#[Group('performance')]
final class PerformanceBenchmarkTest extends TestCase
{
    protected function setUp(): void
    {
        if (getenv('CI') !== false) {
            $this->markTestSkipped(
                'Performance benchmarks are skipped in CI (set CI=false to run locally).',
            );
        }
    }
    // ── Entity Creation ────────────────────────────────────────

    #[Test]
    public function userEntityCreation10000(): void
    {
        $start = hrtime(true);

        for ($i = 0; $i < 10_000; $i++) {
            $user = new User();
            $user->email = "user{$i}@test.com";
            $user->name = "User {$i}";
            $user->password_hash = 'hash';
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        // 10,000 entity creations should complete in under 1000ms
        $this->assertLessThan(
            1000,
            $durationMs,
            "User creation took {$durationMs}ms for 10,000 entities",
        );
    }

    #[Test]
    public function postEntityCreationWithSlug5000(): void
    {
        $start = hrtime(true);

        for ($i = 0; $i < 5_000; $i++) {
            $post = new Post();
            $post->title = "Performance Test Post Number {$i}!";
            $post->slug = "Performance Test Post Number {$i}!";
            $post->body = str_repeat('Lorem ipsum dolor sit amet. ', 10);
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        // 5,000 with slug generation should complete under 1000ms
        $this->assertLessThan(
            1000,
            $durationMs,
            "Post creation with slug took {$durationMs}ms for 5,000 entities",
        );
    }

    #[Test]
    public function commentEntityCreation10000(): void
    {
        $start = hrtime(true);

        for ($i = 0; $i < 10_000; $i++) {
            $comment = new Comment();
            $comment->body = "Comment body number {$i} with some extra text.";
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        $this->assertLessThan(
            600,
            $durationMs,
            "Comment creation took {$durationMs}ms for 10,000 entities",
        );
    }

    // ── Property Hooks ─────────────────────────────────────────

    #[Test]
    public function emailNormalizationHook50000(): void
    {
        $user = new User();
        $user->name = 'Test';

        $start = hrtime(true);

        for ($i = 0; $i < 50_000; $i++) {
            $user->email = "  TEST{$i}@EXAMPLE.COM  ";
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        // 50,000 email normalizations should complete under 400ms
        $this->assertLessThan(
            400,
            $durationMs,
            "Email hook took {$durationMs}ms for 50,000 normalizations",
        );

        // Verify last normalization was correct
        $this->assertSame('test49999@example.com', $user->email);
    }

    #[Test]
    public function slugGenerationHook10000(): void
    {
        $post = new Post();

        $start = hrtime(true);

        for ($i = 0; $i < 10_000; $i++) {
            $post->slug = "Hello World — Performance Test #{$i}!";
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        // 10,000 slug generations should complete under 400ms
        $this->assertLessThan(
            400,
            $durationMs,
            "Slug hook took {$durationMs}ms for 10,000 generations",
        );
    }

    // ── Computed Properties ────────────────────────────────────

    #[Test]
    public function excerptComputedProperty100000(): void
    {
        $post = new Post();
        $post->title = 'Benchmark';
        $post->body = str_repeat('x', 500);
        $post->slug = 'benchmark';

        $start = hrtime(true);

        for ($i = 0; $i < 100_000; $i++) {
            $_ = $post->excerpt;
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        // 100,000 excerpt reads should complete under 600ms
        $this->assertLessThan(
            600,
            $durationMs,
            "Excerpt computed property took {$durationMs}ms for 100,000 reads",
        );
    }

    #[Test]
    public function displayNameComputedProperty100000(): void
    {
        $user = new User();
        $user->name = 'Jorge';
        $user->email = 'jorge@test.com';

        $start = hrtime(true);

        for ($i = 0; $i < 100_000; $i++) {
            $_ = $user->displayName;
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        $this->assertLessThan(
            400,
            $durationMs,
            "displayName computed property took {$durationMs}ms for 100,000 reads",
        );
    }

    // ── Enum Operations ────────────────────────────────────────

    #[Test]
    public function enumOperations100000(): void
    {
        $start = hrtime(true);

        for ($i = 0; $i < 100_000; $i++) {
            $status = OrderStatus::Pending;
            $_ = $status->label();
            $_ = $status->isFinal();
            $_ = $status->color();
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        $this->assertLessThan(
            600,
            $durationMs,
            "Enum operations took {$durationMs}ms for 100,000 iterations",
        );
    }

    // ── DTO Construction ───────────────────────────────────────

    #[Test]
    public function dtoConstruction50000(): void
    {
        $start = hrtime(true);

        for ($i = 0; $i < 50_000; $i++) {
            new CreateUserRequest(
                email: "user{$i}@test.com",
                name: "User {$i}",
                password: 'password123',
            );
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        $this->assertLessThan(
            400,
            $durationMs,
            "DTO construction took {$durationMs}ms for 50,000 instances",
        );
    }

    // ── Resource Serialization ─────────────────────────────────

    #[Test]
    public function userResourceSerialization5000(): void
    {
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->email = "user{$i}@test.com";
            $user->name = "User {$i}";
            $user->password_hash = 'hash';

            $ref = new \ReflectionProperty($user, 'id');
            $ref->setValue($user, $i + 1);
            $ref = new \ReflectionProperty($user, 'created_at');
            $ref->setValue($user, new \DateTimeImmutable());
            $ref = new \ReflectionProperty($user, 'updated_at');
            $ref->setValue($user, new \DateTimeImmutable());

            $users[] = $user;
        }

        $start = hrtime(true);

        for ($i = 0; $i < 50; $i++) {
            UserResource::collection($users);
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        // 50 collection serializations of 100 users each = 5,000 toArray calls
        $this->assertLessThan(
            1000,
            $durationMs,
            "UserResource serialization took {$durationMs}ms for 5,000 toArray calls",
        );
    }

    // ── Password Hashing ───────────────────────────────────────

    #[Test]
    public function passwordHashingPerformance(): void
    {
        $start = hrtime(true);

        for ($i = 0; $i < 5; $i++) {
            $hash = password_hash("password{$i}", PASSWORD_DEFAULT);
            password_verify("password{$i}", $hash);
        }

        $durationMs = (hrtime(true) - $start) / 1e6;

        // 5 hash+verify cycles should complete under 4 seconds
        $this->assertLessThan(
            4000,
            $durationMs,
            "Password hashing took {$durationMs}ms for 5 cycles",
        );
    }
}
