<?php
// Define constants that are available in the app runtime but hidden from static analysis
if (!defined('ML_CONTAINER')) {
    define('ML_CONTAINER', new \MonkeysLegion\DI\ContainerBuilder()->build());
}
if (!defined('ML_BASE_PATH')) {
    define('ML_BASE_PATH', dirname(__DIR__));
}
