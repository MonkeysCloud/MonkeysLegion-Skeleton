<?php
declare(strict_types=1);

use MonkeysLegion\Framework\HttpBootstrap;

define('ML_BASE_PATH', dirname(__DIR__));
require ML_BASE_PATH . '/vendor/autoload.php';

HttpBootstrap::run(ML_BASE_PATH);