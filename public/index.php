<?php
declare(strict_types=1);

use MonkeysLegion\Framework\Application;

define('ML_BASE_PATH', dirname(__DIR__));
require ML_BASE_PATH . '/vendor/autoload.php';

Application::create(basePath: ML_BASE_PATH)->run();