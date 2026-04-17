<?php
declare(strict_types=1);

namespace App\Providers;

use MonkeysLegion\Framework\Attributes\Provider;

/**
 * Application-level service provider.
 *
 * Register custom bindings, tagged services, or
 * interface-to-concrete mappings here.
 */
#[Provider]
class AppProvider
{
    public function __construct() {}

    public function register(): void
    {
        // Example: $container->bind(PaymentGatewayInterface::class, StripeGateway::class);
        // Example: $container->tag(StripeGateway::class, 'payment.processors');
    }
}
