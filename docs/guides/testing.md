# Testing

MonKeysLegion uses PHPUnit 11+ with framework-specific test base classes and helper traits.

## Test Suites

```
tests/
├── Unit/          # Pure unit tests, no framework boot
├── Feature/       # Full HTTP pipeline tests
├── Integration/   # DI container + service tests
└── Performance/   # Benchmarks (PHPBench)
```

## Running Tests

```bash
composer test              # All tests
composer test:unit         # Unit tests only
composer test:feature      # Feature tests only
composer test:integration  # Integration tests only
```

## Test Base Classes

```php
// Unit test — no framework bootstrap
namespace Tests\Unit;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase { ... }

// Feature test — full HTTP pipeline
namespace Tests\Feature;
use Tests\FeatureTestCase;

final class ProductApiTest extends FeatureTestCase { ... }
```

## RefreshDatabase Trait

Reset the database between tests:

```php
use MonkeysLegion\Testing\Concerns\RefreshDatabase;

final class UserTest extends FeatureTestCase
{
    use RefreshDatabase;

    public function test_create_user(): void
    {
        // Database is fresh for each test
        $user = User::create(['name' => 'Test', 'email' => 'test@example.com']);
        $this->assertSame('Test', $user->name);
    }
}
```

## HTTP Testing

```php
final class ProductApiTest extends FeatureTestCase
{
    public function test_list_products(): void
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function test_create_product(): void
    {
        $response = $this->post('/api/products', [
            'name'  => 'Widget',
            'price' => 29.99,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Widget');
    }

    public function test_validation_error(): void
    {
        $response = $this->post('/api/products', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price']);
    }
}
```

## Fakes

### Queue Fake

```php
use MonkeysLegion\Testing\Fakes\QueueFake;

QueueFake::fake();

$this->service->registerUser($data);

QueueFake::assertPushed(SendWelcomeEmailJob::class);
QueueFake::assertNotPushed(FailedJob::class);
```

### Mail Fake

```php
use MonkeysLegion\Testing\Fakes\MailFake;

MailFake::fake();

$this->service->sendWelcomeEmail($user);

MailFake::assertSent(WelcomeEmail::class);
MailFake::assertSentTo($user->email, WelcomeEmail::class);
```

### Event Fake

```php
use MonkeysLegion\Testing\Fakes\EventFake;

EventFake::fake();

$this->service->create($data);

EventFake::assertDispatched(UserCreated::class);
```

## Database Assertions

```php
use MonkeysLegion\Testing\Concerns\InteractsWithDatabase;

final class UserTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    public function test_user_persisted(): void
    {
        $this->post('/users', ['name' => 'Bob']);

        $this->assertDatabaseHas('users', ['name' => 'Bob']);
        $this->assertDatabaseMissing('users', ['name' => 'Deleted']);
    }
}
```

## Snapshot Testing

```php
use MonkeysLegion\Testing\SnapshotAssertions;

final class ViewTest extends TestCase
{
    use SnapshotAssertions;

    public function test_renders_correctly(): void
    {
        $html = $this->renderer->render('products.show', $data);
        $this->assertMatchesSnapshot($html);
    }
}
```

## Factories

```php
final class UserFactory extends Factory
{
    protected string $entity = User::class;

    public function definition(): array
    {
        return [
            'name'  => fake()->name(),
            'email' => fake()->email(),
        ];
    }
}

// Usage
$user = UserFactory::new()->create();
$users = UserFactory::new()->createMany(5);
$user = UserFactory::new()->state(['name' => 'Admin'])->create();
```
