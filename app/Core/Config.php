<?php
namespace App\Core;

use RuntimeException;

class Config
{
    private static $configs = [];
    
    public static function get(string $key): array
    {
        if (isset(self::$configs[$key])) {
            return self::$configs[$key];
        }
        
        $filePath = __DIR__ . '/../../config/' . $key . '.php';
        
        if (!file_exists($filePath)) {
            throw new RuntimeException('Config file not found: ' . $key);
        }
        
        self::$configs[$key] = require $filePath;
        return self::$configs[$key];
    }
    
    public static function item(string $key, $default = null)
    {
        $parts = explode('.', $key, 2);
        $file = $parts[0];
        $item = $parts[1] ?? null;
        
        try {
            $config = self::get($file);
        } catch (RuntimeException $e) {
            return $default;
        }
        
        if ($item === null) {
            return $config;
        }
        
        return $config[$item] ?? $default;
    }
}