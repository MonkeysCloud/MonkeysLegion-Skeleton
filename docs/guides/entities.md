# Entities

Entities are PHP 8.4 classes with attribute-based database mappings. They use property hooks for computed properties and transformations.

## Basic Entity

```php
<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\{Entity, Field, Id, Fillable, Hidden, Index, Timestamps, SoftDeletes};

#[Entity(table: 'products')]
#[Timestamps]
#[SoftDeletes]
#[Index(columns: ['slug'], name: 'idx_products_slug')]
class Product
{
    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 255)]
    #[Fillable]
    public string $name;

    #[Field(type: 'string', length: 300)]
    public string $slug {
        set(string $value) {
            $this->slug = strtolower(trim(
                preg_replace('/[^a-z0-9]+/', '-', strtolower($value)), '-'
            ));
        }
    }

    #[Field(type: 'decimal', precision: 10, scale: 2)]
    #[Fillable]
    public float $price;

    #[Field(type: 'boolean')]
    public bool $active = true;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $updated_at;
}
```

## Field Types

| Type | Description |
|------|-------------|
| `string` | VARCHAR with length |
| `text` | TEXT |
| `int` | INTEGER |
| `unsignedBigInt` | BIGINT UNSIGNED |
| `float` | FLOAT |
| `decimal` | DECIMAL(precision, scale) |
| `boolean` | BOOLEAN/TINYINT |
| `datetime` | DATETIME |
| `date` | DATE |
| `json` | JSON |
| `binary` | BLOB/BINARY |

## Entity Attributes

| Attribute | Purpose |
|-----------|---------|
| `#[Entity(table: 'name')]` | Marks class as entity, sets table name |
| `#[Id]` | Marks primary key field |
| `#[Field(type: ...)]` | Maps property to column |
| `#[Fillable]` | Allows mass assignment |
| `#[Hidden]` | Excludes from JSON serialization |
| `#[Timestamps]` | Adds created_at/updated_at |
| `#[SoftDeletes]` | Adds deleted_at column |
| `#[Index(columns: [...])]` | Creates database index |
| `#[Uuid]` | Uses UUID as primary key |
| `#[Immutable]` | Prevents modification after creation |

## Property Hooks (PHP 8.4)

### Set Hooks — Transform on assignment

```php
#[Field(type: 'string', length: 300)]
public string $slug {
    set(string $value) {
        $this->slug = strtolower(trim(
            preg_replace('/[^a-z0-9]+/', '-', strtolower($value)), '-'
        ));
    }
}
```

### Asymmetric Visibility

```php
public private(set) int $id;        // Read-only externally, writable internally
public private(set) \DateTimeImmutable $created_at;
```

### Computed Properties (Get Hooks)

```php
public string $formattedPrice {
    get => '$' . number_format($this->price, 2);
}

public bool $isExpired {
    get => $this->expires_at < new \DateTimeImmutable();
}
```

## Relations

### ManyToOne (Belongs To)

```php
use MonkeysLegion\Entity\Attributes\ManyToOne;

#[ManyToOne(targetEntity: User::class, foreignKey: 'user_id')]
public private(set) ?User $user;
```

### OneToMany (Has Many)

```php
use MonkeysLegion\Entity\Attributes\OneToMany;

/** @var list<Post> */
#[OneToMany(targetEntity: Post::class, mappedBy: 'user')]
public private(set) array $posts;
```

### ManyToMany (Belongs To Many)

```php
use MonkeysLegion\Entity\Attributes\ManyToMany;

/** @var list<Role> */
#[ManyToMany(targetEntity: Role::class, joinTable: 'user_roles')]
public private(set) array $roles;
```

### OneToOne

```php
use MonkeysLegion\Entity\Attributes\OneToOne;

#[OneToOne(targetEntity: Profile::class, mappedBy: 'user')]
public private(set) ?Profile $profile;
```

## Using Entities

### Create

```php
$product = new Product();
$product->name = 'Widget';
$product->slug = 'Widget';  // Set hook transforms to 'widget'
$product->price = 29.99;

$this->products->persist($product);
```

### Query

```php
$product = $this->products->findOrFail(1);
echo $product->formattedPrice;  // "$29.99"
```
