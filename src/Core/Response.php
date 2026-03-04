<?php

namespace App\Core;

class Response
{
    /**
     * Redirect to URL
     */
    public static function redirect(string $to): void
    {
        // Add base path if path starts with /
        if (strpos($to, '/') === 0) {
            $basePath = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/pos-system';
            $to = $basePath . $to;
        }
        
        header('Location: ' . $to);
        exit;
    }

    /**
     * Return JSON response
     */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Set flash message
     */
    public static function flash(string $key, string $msg): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['_flash'][$key] = $msg;
    }

    /**
     * Get and clear flash message
     */
    public static function getFlash(string $key): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        
        return $msg;
    }
}