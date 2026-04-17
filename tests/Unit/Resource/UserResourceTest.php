<?php
declare(strict_types=1);

namespace Tests\Unit\Resource;

use App\Entity\User;
use App\Resource\UserResource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserResource::class)]
final class UserResourceTest extends TestCase
{
    private function makeUser(): User
    {
        $user = new User();
        $user->email = 'test@example.com';
        $user->name = 'Jorge';
        $user->password_hash = 'hash';
        $user->markEmailVerified();

        $ref = new \ReflectionProperty($user, 'id');
        $ref->setValue($user, 1);
        $ref = new \ReflectionProperty($user, 'created_at');
        $ref->setValue($user, new \DateTimeImmutable('2026-01-01'));
        $ref = new \ReflectionProperty($user, 'updated_at');
        $ref->setValue($user, new \DateTimeImmutable('2026-01-02'));

        return $user;
    }

    #[Test]
    public function toArrayReturnsCorrectStructure(): void
    {
        $user = $this->makeUser();
        $data = UserResource::toArray($user);

        $this->assertSame(1, $data['id']);
        $this->assertSame('users', $data['type']);
        $this->assertSame('test@example.com', $data['attributes']['email']);
        $this->assertSame('Jorge', $data['attributes']['name']);
        $this->assertTrue($data['attributes']['is_verified']);
        $this->assertArrayHasKey('created_at', $data['attributes']);
        $this->assertArrayHasKey('updated_at', $data['attributes']);
    }

    #[Test]
    public function makeReturnsJsonResponse(): void
    {
        $user = $this->makeUser();
        $response = UserResource::make($user);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));

        $body = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('data', $body);
        $this->assertSame(1, $body['data']['id']);
    }

    #[Test]
    public function makeAcceptsCustomStatus(): void
    {
        $user = $this->makeUser();
        $response = UserResource::make($user, 201);

        $this->assertSame(201, $response->getStatusCode());
    }

    #[Test]
    public function collectionReturnsArrayWithMeta(): void
    {
        $users = [$this->makeUser(), $this->makeUser()];
        $response = UserResource::collection($users);

        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertCount(2, $body['data']);
        $this->assertSame(2, $body['meta']['total']);
    }

    #[Test]
    public function collectionHandlesEmptyArray(): void
    {
        $response = UserResource::collection([]);

        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame([], $body['data']);
        $this->assertSame(0, $body['meta']['total']);
    }
}
