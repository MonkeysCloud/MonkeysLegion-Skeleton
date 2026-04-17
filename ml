<?php
declare(strict_types=1);

/**
 * MonkeysLegion CLI entry point (php ml).
 *
 * Boots the v2 Application and delegates to the CLI kernel automatically
 * when running under PHP_SAPI === 'cli'.
 */

use MonkeysLegion\Framework\Application;

// 1) Locate autoloader
$dir = __DIR__;
$autoload = $dir . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    fwrite(STDERR, "Error: could not find vendor/autoload.php in {$dir}\n");
    exit(1);
}

require_once $autoload;
define('ML_BASE_PATH', $dir);

// 2) Boot and run — Application detects CLI SAPI and runs CliKernel
Application::create(basePath: ML_BASE_PATH)->run();
