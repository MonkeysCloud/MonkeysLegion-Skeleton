<?php
declare(strict_types=1);

/**
 * MonkeysLegion v2 Bootstrap — returns a booted DI Container.
 *
 * Used by CLI entry points and integration tests that need
 * the container without running the full HTTP lifecycle.
 *
 * @return \MonkeysLegion\DI\Container
 */

use MonkeysLegion\Framework\Application;

if (!defined('ML_BASE_PATH')) {
    define('ML_BASE_PATH', __DIR__);
}

require_once ML_BASE_PATH . '/vendor/autoload.php';

return Application::create(basePath: ML_BASE_PATH)->boot();