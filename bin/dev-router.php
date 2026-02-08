#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * PHP built-in server router – bin wrapper.
 *
 * Delegates to the vendor monkeyslegion-dev-server router script.
 * The dev-server binary already looks for bin/dev-router.php first,
 * then falls back to the vendor copy. This file ensures the
 * composer bin reference is valid and can serve as a project-level
 * override point.
 */

$projectRoot  = dirname(__DIR__);
$vendorRouter = $projectRoot . '/vendor/monkeyscloud/monkeyslegion-dev-server/bin/dev-router.php';

if (! is_file($vendorRouter)) {
    fwrite(STDERR, "Error: vendor router not found. Run: composer install\n");
    exit(1);
}

require $vendorRouter;
