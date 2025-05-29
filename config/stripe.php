<?php

return [
    'secret_key'      => getenv('STRIPE_SECRET_KEY') ?: '',
    'publishable_key' => getenv('STRIPE_PUBLISHABLE_KEY') ?: '',
    'webhook_secret'  => getenv('STRIPE_WEBHOOK_SECRET') ?: '',
    'api_version'     => getenv('STRIPE_API_VERSION') ?: '2025-04-30',
    'currency'        => getenv('STRIPE_CURRENCY') ?: 'usd',
];
