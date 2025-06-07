<?php

return [
    'secret_key'      => $_ENV['STRIPE_SECRET_KEY'] ?? '',
    'publishable_key' => $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '',
    'webhook_secret'  => $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '',
    'api_version'     => $_ENV['STRIPE_API_VERSION'] ?? '2025-04-30',
    'currency'        => $_ENV['STRIPE_CURRENCY'] ?? 'usd',
    'test_key'       => $_ENV['STRIPE_TEST_KEY'] ?? '',
];
