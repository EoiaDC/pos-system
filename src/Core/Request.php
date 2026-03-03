<?php

namespace POS\Core;

class Request
{
    /**
     * Get HTTP method
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get path without query string and base path
     */
    public static function path(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }
        
        // Remove base path if present (APP_BASE_PATH)
        $basePath = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/pos-system';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Ensure leading slash
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . $path;
        }
        
        return $path;
    }

    /**
     * Get input value from POST or GET
     */
    public static function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Get all input (POST + GET, POST takes precedence)
     */
    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Get client IP address
     */
    public static function ip(): string
    {
        $headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }
        
        return '0.0.0.0';
    }

    /**
     * Get user agent
     */
    public static function ua(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}