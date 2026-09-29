# Validation

Validation is done via attributes on DTO (Data Transfer Object) properties. DTOs are auto-hydrated from the request body and validated before reaching the controller.

## Request DTOs

```php
<?php
declare(strict_types=1);

namespace App\Dto;

use MonkeysLegion\Validation\Attributes\{NotBlank, Length, Email, Range, Choice};

final readonly class CreateUserRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(min: 2, max: 255)]
        public string $name,

        #[NotBlank]
        #[Email]
        public string $email,

        #[NotBlank]
        #[Length(min: 8, max: 64)]
        public string $password,

        #[Range(min: 18, max: 120)]
        public int $age,

        #[Choice(['admin', 'user', 'guest'])]
        public string $role = 'user',
    ) {}
}
```

## Using DTOs in Controllers

```php
#[Route('POST', '/users', name: 'users.create')]
public function create(CreateUserRequest $dto): Response
{
    // $dto is already validated — if invalid, a 422 is returned automatically
    $user = $this->service->create($dto);
    return Response::json(['data' => $user], 201);
}
```

## Validation Attributes

| Attribute | Purpose |
|-----------|---------|
| `#[NotBlank]` | Value must not be empty/null |
| `#[Length(min, max)]` | String length constraint |
| `#[Email]` | Must be a valid email address |
| `#[Range(min, max)]` | Numeric range constraint |
| `#[Choice([...])]` | Must be one of the allowed values |
| `#[Pattern(regex)]` | Must match a regex pattern |
| `#[Url]` | Must be a valid URL |
| `#[Date]` | Must be a valid date |
| `#[DateTime]` | Must be a valid datetime |
| `#[Uuid]` | Must be a valid UUID |
| `#[Unique(entity, field)]` | Must be unique in the database |
| `#[WhenExists]` | Validate only when the field is present |

## Validation Error Response

When validation fails, a 422 Unprocessable Entity response is returned:

```json
{
    "errors": {
        "email": ["The email field must be a valid email address."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

## Custom Validation

Create custom validation rules by implementing the validator interface:

```php
#[Attribute]
final class StrongPassword implements ValidationAttribute
{
    public function validate(mixed $value, ValidationContext $ctx): ?string
    {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $value)) {
            return 'Password must contain uppercase, lowercase, and a number.';
        }
        return null; // null = valid
    }
}
```

## Conditional Validation

```php
public function __construct(
    #[NotBlank]
    #[Length(min: 2, max: 255)]
    public string $name,

    #[WhenExists]
    #[Email]
    public ?string $email = null,  // Only validated if present
) {}
```
