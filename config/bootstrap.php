<?php
// Start session for flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', realpath(__DIR__ . '/..'));

require __DIR__ . '/env.php';

// Load .env if present
env_load(BASE_PATH . DIRECTORY_SEPARATOR . '.env');

// Load Core helpers
require_once __DIR__ . '/../src/Core/Request.php';
require_once __DIR__ . '/../src/Core/Response.php';
require_once __DIR__ . '/../src/Core/Validator.php';

$app = require __DIR__ . '/app.php';

date_default_timezone_set($app['timezone'] ?? 'Asia/Manila');

define('APP_ENV', (string)($app['env'] ?? 'local'));
define('APP_DEBUG', (bool)($app['debug'] ?? false));

// Ensure storage dirs exist
$logDir = BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
$cacheDir = BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache';

if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0777, true);