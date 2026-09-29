# Cache

MonKeysLegion provides a PSR-16 compliant cache with Redis, File, Array, and Memcached drivers.

## Configuration

```hocon
# config/cache.mlc
cache {
    default = ${CACHE_DRIVER:redis}

    stores {
        redis {
            driver = "redis"
            prefix = "ml_cache:"
            ttl    = 3600
        }
        file {
            driver = "file"
            path   = "var/cache"
        }
        array {
            driver = "array"
        }
        memcached {
            driver  = "memcached"
            servers = [["localhost", 11211]]
        }
    }
}
```

## Basic Usage

```php
use Psr\SimpleCache\CacheInterface;

final class ProductService
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly ProductRepository $products,
    ) {}

    public function findActive(): array
    {
        return $this->cache->get('products.active', function(): array {
            return $this->products->findActive();
        });
    }
}
```

## Cache Methods

```php
// Get with default
$value = $cache->get('key', 'default-value');

// Get with callback (computed on miss)
$value = $cache->get('key', fn() => expensiveComputation());

// Set with TTL (seconds)
$cache->set('key', $value, 3600);

// Set forever (TTL = 0/null)
$cache->set('key', $value, null);

// Delete
$cache->delete('key');

// Multiple operations
$cache->setMultiple(['a' => 1, 'b' => 2], 3600);
$values = $cache->getMultiple(['a', 'b']);

// Clear all
$cache->clear();

// Check existence
if ($cache->has('key')) { ... }
```

## Repository Caching

```php
class CachedProductRepository extends EntityRepository
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {}

    public function findActive(): array
    {
        return $this->cache->get('products.active', function(): array {
            return parent::findActive();
        });
    }

    public function persist(Product $product): void
    {
        parent::persist($product);
        $this->cache->delete('products.active');
    }
}
```

## Cache Tags

Tagged cache allows bulk invalidation:

```php
$cache->tags(['products', 'featured'])->set('key', $value, 3600);

// Invalidate all cached items with a tag
$cache->tags('products')->flush();
```

## Query Result Caching

The query builder supports cache directly:

```php
$products = $this->query()
    ->where('active', true)
    ->orderBy('name')
    ->remember(3600, 'products.active.list')
    ->get();
```

## Cache Drivers

| Driver | Use Case |
|--------|----------|
| `redis` | Production (fast, supports tags, TTL) |
| `file` | Development without Redis |
| `array` | Testing (in-memory, cleared per request) |
| `memcached` | Alternative to Redis |

## Cache CLI

```bash
php bin/ml cache:clear           # Clear the application cache
php bin/ml cache:clear --store=redis  # Clear a specific store
```
