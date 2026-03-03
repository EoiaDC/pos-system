<?php
namespace App\Core;

use Dotenv\Dotenv;
use RuntimeException;

class Env
{
    private static $loaded = false;
    
    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }
        
        if (!file_exists($path . '/.env')) {
            throw new RuntimeException('.env file not found.');
        }
        
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
        
        self::$loaded = true;
    }
    
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
