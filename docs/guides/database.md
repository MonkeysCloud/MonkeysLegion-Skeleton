# Database

MonKeysLegion provides a full database layer: migrations, seeders, factories, query builder, and repository pattern.

## Configuration

```hocon
# config/database.mlc
database {
    default = "mysql"

    connections {
        mysql {
            driver   = "mysql"
            host     = ${DB_HOST:localhost}
            port     = ${DB_PORT:3306}
            database = ${DB_DATABASE:monkeyslegion}
            username = ${DB_USERNAME:root}
            password = ${DB_PASSWORD:""}
            charset  = "utf8mb4"
        }
        sqlite {
            driver   = "sqlite"
            database = ${DB_DATABASE:storage/app.db}
        }
        pgsql {
            driver   = "pgsql"
            host     = ${DB_HOST:localhost}
            port     = ${DB_PORT:5432}
            database = ${DB_DATABASE:monkeyslegion}
        }
    }
}
```

## Migrations

### Creating Migrations

```bash
php bin/ml make:migration create_users_table
php bin/ml make:migration add_status_to_posts_table
```

Migration files use timestamped filenames in `database/migrations/`.

### Migration Structure

```php
return [
    'up' => [
        'CREATE TABLE users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )',
    ],
    'down' => [
        'DROP TABLE users',
    ],
];
```

### Running Migrations

```bash
php bin/ml migrate              # Run pending migrations
php bin/ml migrate:status       # Show migration status
php bin/ml migrate:rollback     # Rollback last batch
php bin/ml migrate:refresh      # Rollback all + re-run
php bin/ml migrate:fresh        # Drop all tables + re-run
```

## Seeders

```php
<?php
declare(strict_types=1);

namespace Database\Seeders;

use MonkeysLegion\Database\Seeder;

final class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->insert('users', [
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => password_hash('secret', PASSWORD_BCRYPT),
        ]);
    }
}
```

```bash
php bin/ml seed                 # Run all seeders
php bin/ml seed --class=UsersSeeder  # Run a specific seeder
php bin/ml make:seeder UsersSeeder    # Generate a seeder
```

## Factories

Factories generate test data:

```php
final class UserFactory extends Factory
{
    protected function entity(): string { return User::class; }

    public function definition(): array
    {
        return [
            'name'  => fake()->name(),
            'email' => fake()->email(),
        ];
    }
}

// Usage:
UserFactory::new()->create();
UserFactory::new()->createMany(10);
UserFactory::new()->state(['name' => 'Admin'])->create();
```

## Repository Pattern

```php
class UserRepository extends EntityRepository
{
    protected string $table = 'users';
    protected string $entityClass = User::class;

    /** @return list<User> */
    public function findActive(): array
    {
        return $this->findBy(
            criteria: ['active' => true],
            orderBy: ['name' => 'ASC'],
        );
    }

    public function search(string $term): array
    {
        return $this->query()
            ->where('name', 'LIKE', "%{$term}%")
            ->where('active', '=', true)
            ->orderBy('name', 'ASC')
            ->get();
    }
}
```

## Query Builder

```php
$users = $this->query()
    ->select(['id', 'name', 'email'])
    ->where('active', '=', true)
    ->whereIn('role', ['admin', 'user'])
    ->orderBy('name', 'ASC')
    ->limit(20)
    ->offset(0)
    ->get();

// Relationship queries
$users = $this->query()
    ->whereHas('posts', fn($q) => $q->where('published', true))
    ->get();

// Aggregates
$count = $this->query()->where('active', true)->count();
```

## CLI Database Commands

| Command | Description |
|---------|-------------|
| `ml db:create` | Create the database |
| `ml db:wipe` | Drop all tables |
| `ml db:monitor` | Monitor queries in real-time |
| `ml migrate` | Run migrations |
| `ml migrate:status` | Show migration status |
| `ml seed` | Run seeders |
