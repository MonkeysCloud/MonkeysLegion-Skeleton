# Feature Flags

MonKeysLegion provides a Pennant-style feature flag system with support for boolean flags, percentage rollout, A/B testing, and per-scope evaluation.

## Configuration

```hocon
# config/feature-flags.mlc
feature_flags {
    driver = "memory"  # memory | database | redis
    table  = "feature_flags"
}
```

## Usage

### Define Flags

```php
use MonkeysLegion\FeatureFlags\Feature;

// Simple boolean
Feature::defineBool('new-ui', false);

// Percentage rollout (25% of users)
Feature::definePercentage('beta-access', 25);

// A/B test (70/30 split)
Feature::defineAB('button-color', [
    'red'  => 70,
    'blue' => 30,
]);
```

### Check Flags

```php
// Global check
if (Feature::isActive('new-ui')) {
    // render new UI
}

// Per-scope check
if (Feature::for($user)->isActive('beta-access')) {
    // show beta features
}

// Get A/B variant
$variant = Feature::value('button-color', $user);
```

### Route-level Flag Gating

```php
use MonkeysLegion\FeatureFlags\Attribute\FeatureFlag;

#[FeatureFlag('new-api')]
public function apiEndpoint(): Response
{
    // Only accessible if 'new-api' flag is active
}
```

## Drivers

| Driver | Use Case |
|--------|----------|
| `memory` | Testing, development |
| `database` | Production with SQL database |
| `redis` | High-performance production |

## Database Schema

```sql
CREATE TABLE feature_flags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    scope VARCHAR(255) NOT NULL DEFAULT '__global__',
    value TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_flag_scope (name, scope)
);
```
