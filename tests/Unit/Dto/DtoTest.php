<?php
declare(strict_types=1);

namespace Tests\Unit\Dto;

use App\Dto\UpdateUserRequest;
use App\Dto\CreatePostRequest;
use App\Dto\LoginRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UpdateUserRequest::class)]
#[CoversClass(CreatePostRequest::class)]
#[CoversClass(LoginRequest::class)]
final class DtoTest extends TestCase
{
    // ── UpdateUserRequest ──────────────────────────────────────

    #[Test]
    public function updateUserAllFieldsNullable(): void
    {
        $dto = new UpdateUserRequest();

        $this->assertNull($dto->email);
        $this->assertNull($dto->name);
        $this->assertNull($dto->password);
    }

    #[Test]
    public function updateUserPartialUpdate(): void
    {
        $dto = new UpdateUserRequest(email: 'new@test.com');

        $this->assertSame('new@test.com', $dto->email);
        $this->assertNull($dto->name);
        $this->assertNull($dto->password);
    }

    #[Test]
    public function updateUserFullUpdate(): void
    {
        $dto = new UpdateUserRequest(
            email: 'new@test.com',
            name: 'New Name',
            password: 'new-password-123',
        );

        $this->assertSame('new@test.com', $dto->email);
        $this->assertSame('New Name', $dto->name);
        $this->assertSame('new-password-123', $dto->password);
    }

    #[Test]
    public function updateUserIsReadonly(): void
    {
        $ref = new \ReflectionClass(UpdateUserRequest::class);
        $this->assertTrue($ref->isReadOnly());
    }

    // ── CreatePostRequest ──────────────────────────────────────

    #[Test]
    public function createPostWithRequiredFields(): void
    {
        $dto = new CreatePostRequest(title: 'My Post', body: 'Content');

        $this->assertSame('My Post', $dto->title);
        $this->assertSame('Content', $dto->body);
        $this->assertSame('draft', $dto->status);
    }

    #[Test]
    public function createPostWithCustomStatus(): void
    {
        $dto = new CreatePostRequest(
            title: 'My Post',
            body: 'Content',
            status: 'published',
        );

        $this->assertSame('published', $dto->status);
    }

    #[Test]
    public function createPostIsReadonly(): void
    {
        $ref = new \ReflectionClass(CreatePostRequest::class);
        $this->assertTrue($ref->isReadOnly());
    }

    #[Test]
    public function createPostHasValidationAttributes(): void
    {
        $ref = new \ReflectionClass(CreatePostRequest::class);
        $titleProp = $ref->getProperty('title');
        $attrNames = array_map(
            fn(\ReflectionAttribute $a) => $a->getName(),
            $titleProp->getAttributes(),
        );

        $this->assertContains('MonkeysLegion\Validation\Attributes\NotBlank', $attrNames);
        $this->assertContains('MonkeysLegion\Validation\Attributes\Length', $attrNames);
    }

    // ── LoginRequest ───────────────────────────────────────────

    #[Test]
    public function loginRequestConstruction(): void
    {
        $dto = new LoginRequest(
            email: 'user@test.com',
            password: 'password123',
        );

        $this->assertSame('user@test.com', $dto->email);
        $this->assertSame('password123', $dto->password);
    }

    #[Test]
    public function loginRequestIsReadonly(): void
    {
        $ref = new \ReflectionClass(LoginRequest::class);
        $this->assertTrue($ref->isReadOnly());
    }

    #[Test]
    public function loginRequestHasEmailValidation(): void
    {
        $ref = new \ReflectionClass(LoginRequest::class);
        $emailProp = $ref->getProperty('email');
        $attrNames = array_map(
            fn(\ReflectionAttribute $a) => $a->getName(),
            $emailProp->getAttributes(),
        );

        $this->assertContains('MonkeysLegion\Validation\Attributes\NotBlank', $attrNames);
        $this->assertContains('MonkeysLegion\Validation\Attributes\Email', $attrNames);
    }
}
