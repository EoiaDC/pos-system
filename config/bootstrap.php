<?php

define('APP_BASE_PATH', '/pos-system/public');
// Database configuration (temporary – replace with your actual credentials)
define('DB_HOST', 'localhost');
define('DB_NAME', 'pos_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is empty

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}

function flash($key, $message = null) {
    if ($message) {
        $_SESSION['flash'][$key] = $message;
    } elseif (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

// PSR-4 autoloader for both App and POS namespaces
spl_autoload_register(function ($class) {
    // Handle App\ namespace
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) require $file;
    }
    
    // Handle POS\ namespace
    if (strpos($class, 'POS\\') === 0) {
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) require $file;
    }
});



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
require_once __DIR__ . '/../src/Auth/Auth.php';
require_once __DIR__ . '/../src/Auth/Rbac.php';

$app = require __DIR__ . '/app.php';

date_default_timezone_set($app['timezone'] ?? 'Asia/Manila');

define('APP_ENV', (string)($app['env'] ?? 'local'));
define('APP_DEBUG', (bool)($app['debug'] ?? false));

// Ensure storage dirs exist
$logDir = BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
$cacheDir = BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache';

if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0777, true);