<?php

namespace App\Client;

use Psr\Http\Client\ClientInterface;

class StripeClient
{
    private $httpClient;
    private $secretKey;

    public function __construct(string $secretKey, ClientInterface $httpClient)
    {
        $this->secretKey = $secretKey;
        $this->httpClient = $httpClient;
    }

    // Stripe client methods here, using $this->httpClient to make HTTP calls
}
