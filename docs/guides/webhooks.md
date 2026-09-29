# Webhooks

MonKeysLegion provides a full-featured webhook management system with HMAC signing, retry logic, and delivery tracking.

## Configuration

```hocon
# config/webhooks.mlc
webhooks {
    driver     = "memory"    # memory | database
    secret     = ${WEBHOOK_SECRET:""}
    algorithm  = "sha256"
    timeout    = 30
    max_retries = 3
}
```

## Usage

### Register a Webhook

```php
use MonkeysLegion\Webhooks\Webhook;
use MonkeysLegion\Webhooks\WebhookManager;

$manager = $container->get(WebhookManager::class);

$manager->register(new Webhook(
    id: 'wh-001',
    url: 'https://example.com/webhook',
    events: ['order.created', 'order.updated'],
    secret: 'shared-secret',
    active: true,
    maxRetries: 5,
));
```

### Dispatch Events

```php
$deliveries = $manager->dispatch('order.created', [
    'order_id' => 42,
    'total'    => 99.99,
    'currency' => 'USD',
]);

// Each delivery tracks: status, HTTP code, response, attempt count
foreach ($deliveries as $delivery) {
    echo $delivery->status;       // success | failed
    echo $delivery->statusCode;   // 200
    echo $delivery->attempt;      // 1
}
```

### Track Deliveries

```php
$history = $manager->deliveries('wh-001', limit: 50);
foreach ($history as $delivery) {
    echo "{$delivery->event} → {$delivery->status} (attempt {$delivery->attempt})";
}
```

### Verify Incoming Webhooks

```php
use MonkeysLegion\Webhooks\Middleware\VerifyWebhookSignatureMiddleware;

// Add to your middleware pipeline
$middleware = new VerifyWebhookSignatureMiddleware(
    signer: new WebhookSigner($secret),
);
```

## Signing

Outbound webhooks are signed with HMAC-SHA256:

```
X-Webhook-Signature: sha256=<hex digest>
X-Webhook-Event: order.created
X-Webhook-Id: wh_del_abc123
```

## Retry Logic

Failed deliveries are retried with exponential backoff:
- Attempt 1: immediate
- Attempt 2: +1 second
- Attempt 3: +4 seconds
- Max retries is configurable per webhook (default: 3)

## Database Schema

```sql
CREATE TABLE webhooks (
    id VARCHAR(255) PRIMARY KEY,
    url TEXT NOT NULL,
    events JSON,
    secret VARCHAR(255),
    active BOOLEAN DEFAULT TRUE,
    headers JSON,
    max_retries INT DEFAULT 3,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE webhook_deliveries (
    id VARCHAR(255) PRIMARY KEY,
    webhook_id VARCHAR(255) NOT NULL,
    event VARCHAR(255) NOT NULL,
    payload JSON,
    status VARCHAR(50),
    status_code INT,
    response TEXT,
    attempt INT DEFAULT 0,
    delivered_at DATETIME,
    error TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
