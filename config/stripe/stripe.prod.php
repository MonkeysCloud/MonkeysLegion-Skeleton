<?php

return [
    // ── Stripe configuration ──────────────────────────────────────────

    // The secret key for authenticating API requests to Stripe.
    'secret_key'      => $_ENV['STRIPE_SECRET_KEY'] ?? '',

    // The publishable key for client-side Stripe integrations.
    'publishable_key' => $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '',

    // The secret used to verify the authenticity of Stripe webhooks.
    'webhook_secret'  => $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '',

    // The test key for Stripe's test environment.
    'test_key'       => $_ENV['STRIPE_TEST_KEY'] ?? '',

    // The API version to use for Stripe requests.
    'api_version'     => $_ENV['STRIPE_API_VERSION'] ?? '2025-04-30',

    // The default currency for transactions.
    'currency'        => $_ENV['STRIPE_CURRENCY'] ?? 'usd',

    // The maximum amount (in smallest currency unit) allowed per transaction.
    'currency_limit'  => (int)($_ENV['STRIPE_CURRENCY_LIMIT'] ?? 100000), // 100000 cents = $1,000.00

    // The time tolerance (in seconds) for validating webhook signatures.
    'webhook_tolerance' => (int)($_ENV['STRIPE_WEBHOOK_TOLERANCE'] ?? 20),

    // The default time-to-live (in seconds) for webhook events.
    'webhook_default_ttl' => (int)($_ENV['STRIPE_WEBHOOK_DEFAULT_TTL'] ?? 172800),

    // The database table used for storing idempotency events.
    'idempotency_table' => $_ENV['STRIPE_IDEMPOTENCY_TABLE'] ?? 'stripe_memory',

    // The timeout (in seconds) for Stripe API requests.
    'timeout' => (int)($_ENV['STRIPE_TIMEOUT'] ?? 60),

    // The maximum number of retries for failed webhook events.
    'webhook_retries' => (int)($_ENV['STRIPE_WEBHOOK_RETRIES'] ?? 3),

    // The URL for Stripe's API endpoint.
    'api_url' => $_ENV['STRIPE_API_URL'] ?? 'https://api.stripe.com',
];
