<?php
declare(strict_types=1);

namespace Tests\Unit\Dto;

use App\Dto\CreateUserRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateUserRequest::class)]
final class CreateUserRequestTest extends TestCase
{
    #[Test]
    public function constructsWithValidData(): void
    {
        $dto = new CreateUserRequest(
            email: 'test@example.com',
            name: 'Jorge',
            password: 'secure-password-123',
        );

        $this->assertSame('test@example.com', $dto->email);
        $this->assertSame('Jorge', $dto->name);
        $this->assertSame('secure-password-123', $dto->password);
    }

    #[Test]
    public function isReadonly(): void
    {
        $dto = new CreateUserRequest(
            email: 'test@example.com',
            name: 'Jorge',
            password: 'password123',
        );

        $ref = new \ReflectionClass($dto);

        $this->assertTrue($ref->isReadOnly());
    }

    #[Test]
    public function hasValidationAttributes(): void
    {
        $ref = new \ReflectionClass(CreateUserRequest::class);

        $emailProp = $ref->getProperty('email');
        $emailAttrs = $emailProp->getAttributes();
        $attrNames = array_map(fn(\ReflectionAttribute $a) => $a->getName(), $emailAttrs);

        $this->assertContains('MonkeysLegion\Validation\Attributes\NotBlank', $attrNames);
        $this->assertContains('MonkeysLegion\Validation\Attributes\Email', $attrNames);
    }

    #[Test]
    public function passwordHasLengthConstraint(): void
    {
        $ref = new \ReflectionClass(CreateUserRequest::class);
        $passwordProp = $ref->getProperty('password');
        $attrs = $passwordProp->getAttributes();
        $attrNames = array_map(fn(\ReflectionAttribute $a) => $a->getName(), $attrs);

        $this->assertContains('MonkeysLegion\Validation\Attributes\Length', $attrNames);
    }
}
