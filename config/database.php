<?php
/**
 * Database Configuration
 * 
 * @package POS\Config
 */

// Load .env file if it exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Helper function to get env values
if (!function_exists('env')) {
    function env($key, $default = null) {
        // Check $_ENV first
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Check getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Return default
        return $default;
    }
}

// Return database configuration
return [
    'host'    => env('DB_HOST', 'localhost'),
    'port'    => env('DB_PORT', '3306'),
    'dbname'  => env('DB_DATABASE', 'pos_db'),
    'user'    => env('DB_USERNAME', 'root'),
    'pass'    => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4')
];