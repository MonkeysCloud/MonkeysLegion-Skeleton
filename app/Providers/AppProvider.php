<?php

declare(strict_types=1);

namespace App\Providers;

use MonkeysLegion\Contracts\AbstractServiceProvider;
use MonkeysLegion\Framework\Attributes\Provider;

/**
 * Application-level service provider.
 *
 * Register custom bindings, tagged services, or
 * interface-to-concrete mappings here.
 *
 * This provider is auto-discovered by ProviderScanner because it:
 *   1. Extends AbstractServiceProvider (implements ServiceProviderInterface)
 *   2. Has the #[Provider] attribute for discovery metadata
 */
#[Provider]
class AppProvider extends AbstractServiceProvider
{
    /**
     * Return DI definitions for this application.
     *
     * @return array<string, callable|object>
     */
    public function getDefinitions(): array
    {
        return [
            // Example: PaymentGatewayInterface::class => fn() => new StripeGateway(),
        ];
    }
}
