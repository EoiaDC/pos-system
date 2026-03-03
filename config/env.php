<?php

function env_load(string $envFilePath): void
{
    // DEBUG - Check what's happening
    error_log("=== ENV LOAD DEBUG ===");
    error_log("Path: " . $envFilePath);
    error_log("File exists? " . (file_exists($envFilePath) ? 'YES' : 'NO'));
    
    if (!file_exists($envFilePath)) {
        error_log("ERROR: .env file not found at: " . $envFilePath);
        return; // safe: .env may not exist yet
    }

    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        error_log("ERROR: Failed to read .env file");
        return;
    }

    error_log("Found " . count($lines) . " lines in .env");

    foreach ($lines as $index => $line) {
        $originalLine = $line;
        $line = trim($line);
        
        error_log("Processing line " . ($index+1) . ": " . $line);

        if ($line === '' || str_starts_with($line, '#')) {
            error_log("  → Skipping (empty or comment)");
            continue;
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            error_log("  → Skipping (no = found)");
            continue;
        }

        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if ($key === '') {
            error_log("  → Skipping (empty key)");
            continue;
        }

        // Strip surrounding quotes
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
            error_log("  → Stripped quotes, value now: " . $value);
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        
        error_log("  → SET: {$key} = " . (strpos($key, 'PASS') ? '********' : $value));
    }
    
    error_log("=== ENV LOAD COMPLETE ===");
}

function env_get(string $key, $default = null)
{
    $val = getenv($key);
    error_log("env_get('{$key}') = " . ($val ?: 'NULL') . " (default: " . ($default ?: 'NULL') . ")");
    
    if ($val === false || $val === null || $val === '') {
        return $default;
    }
    return $val;
}

function env_bool(string $key, bool $default = false): bool
{
    $val = strtolower((string) env_get($key, $default ? 'true' : 'false'));
    $result = in_array($val, ['1', 'true', 'yes', 'y', 'on'], true);
    error_log("env_bool('{$key}') = " . ($result ? 'true' : 'false'));
    return $result;
}

// Compatibility: Map DB_DATABASE to DB_NAME for older code
if (!getenv('DB_NAME') && getenv('DB_DATABASE')) {
    putenv('DB_NAME=' . getenv('DB_DATABASE'));
    $_ENV['DB_NAME'] = $_ENV['DB_DATABASE'];
    error_log("COMPAT: Mapped DB_DATABASE to DB_NAME");
}

if (!getenv('DB_USER') && getenv('DB_USERNAME')) {
    putenv('DB_USER=' . getenv('DB_USERNAME'));
    $_ENV['DB_USER'] = $_ENV['DB_USERNAME'];
    error_log("COMPAT: Mapped DB_USERNAME to DB_USER");
}