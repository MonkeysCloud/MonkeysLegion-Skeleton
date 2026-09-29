<?php
declare(strict_types=1);

/**
 * MonKeysLegion Framework — Pest PHP Bootstrap
 *
 * Load this file in your phpunit.xml as a bootstrap to enable Pest support.
 *
 * Usage in tests/Pest.php:
 *   <?php
 *   declare(strict_types=1);
 *
 *   uses(\MonkeysLegion\Testing\TestCase::class);
 *   uses(\MonkeysLegion\Testing\PestAdapter::class);
 *   uses(\MonkeysLegion\Testing\Concerns\RefreshDatabase::class);
 *   uses(\MonkeysLegion\Testing\Concerns\InteractsWithDatabase::class);
 *   uses(\MonkeysLegion\Testing\Concerns\FakesServices::class);
 *
 *   // Architecture testing
 *   arch('it uses strict types in all PHP files')
 *       ->expect('App\\')
 *       ->toUseStrictTypes();
 *
 *   arch('controllers are final')
 *       ->expect('App\\Controller\\')
 *       ->toBeFinal();
 *
 *   arch('entities use strict types')
 *       ->expect('App\\Entity\\')
 *       ->toUseStrictTypes();
 *
 *   // Example test
 *   it('can get homepage', function () {
 *       $this->get('/')->assertOk();
 *   });
 */

// This file is a documentation bootstrap. It only loads if Pest is installed.
if (!class_exists(\Pest\Pest::class)) {
    return;
}

// Register global helper functions for Pest tests
if (!function_exists('fakeQueue')) {
    function fakeQueue(): \MonkeysLegion\Testing\Fakes\QueueFake
    {
        return test()->fakeQueue();
    }
}

if (!function_exists('fakeMail')) {
    function fakeMail(): \MonkeysLegion\Testing\Fakes\MailFake
    {
        return test()->fakeMail();
    }
}

if (!function_exists('fakeEvents')) {
    function fakeEvents(): \MonkeysLegion\Testing\Fakes\EventFake
    {
        return test()->fakeEvents();
    }
}
