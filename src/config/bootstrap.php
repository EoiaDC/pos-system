<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base path constant if not defined
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', '/pos-system/public');
}

// Simple autoloader that handles multiple namespaces
spl_autoload_register(function ($class) {
    // Map namespaces to directories
    $prefixes = [
        'POS\\' => __DIR__ . '/../',
        'App\\' => __DIR__ . '/../',
        'Pos\\' => __DIR__ . '/../'
    ];
    
    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load database config
$dbConfig = require __DIR__ . '/../../config/database.php';