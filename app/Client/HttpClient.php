<?php

namespace App\Client;

use App\Services\ServiceContainer;
use GuzzleHttp\Client;

$container = new ServiceContainer();

$container->set('http_client', function () {
    return new Client();
});

$container->set('stripe_client', function ($c) {
    $stripeConfig = require __DIR__ . '/../../config/stripe.php';
    $secretKey = $stripeConfig['secret_key'];
    $httpClient = $c->get('http_client');
    return new StripeClient($secretKey, $httpClient);
});
