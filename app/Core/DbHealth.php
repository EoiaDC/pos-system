<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Class DbHealth
 * 
 * Database connectivity testing utility.
 * This is YOUR module's test harness. Own it.
 */
final class DbHealth
{
    private const OUTPUT_WIDTH = 60;

    /**
     * Run complete database health check
     * 
     * @return array Test results with status and details
     */
    public static function runAllChecks(): array
    {
        $results = [
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => PHP_OS . ' | PHP ' . PHP_VERSION,
            'tests' => []
        ];

        // Test 1: Configuration Loaded
        try {
            $config = self::getConfigFromEnv();
            $results['tests']['config'] = [
                'status' => 'PASS',
                'message' => 'Configuration loaded successfully',
                'details' => [
                    'host' => $config['host'],
                    'database' => $config['name'],
                    'user' => $config['user'],
                    'charset' => $config['charset']
                ]
            ];
        } catch (\Throwable $e) {
            $results['tests']['config'] = [
                'status' => 'FAIL',
                'message' => 'Configuration error: ' . $e->getMessage(),
                'details' => []
            ];
            return $results; // Can't proceed without config
        }

        // Test 2: PDO Extension
        $results['tests']['pdo_extension'] = [
            'status' => extension_loaded('pdo_mysql') ? 'PASS' : 'FAIL',
            'message' => extension_loaded('pdo_mysql')
                ? 'PDO MySQL extension available'
                : 'PDO MySQL extension NOT loaded',
            'details' => []
        ];

        if ($results['tests']['pdo_extension']['status'] === 'FAIL') {
            return $results;
        }

        // Test 3: Raw Connection
        try {
            Database::initConfig($config);
            $connected = Database::testConnection();

            $results['tests']['connection'] = [
                'status' => $connected ? 'PASS' : 'FAIL',
                'message' => $connected
                    ? 'Successfully connected to database'
                    : 'Failed to connect to database',
                'details' => []
            ];
        } catch (\Throwable $e) {
            $results['tests']['connection'] = [
                'status' => 'FAIL',
                'message' => 'Connection exception: ' . $e->getMessage(),
                'details' => []
            ];
        }

        // Test 4: Server Information
        if ($results['tests']['connection']['status'] === 'PASS') {
            $info = Database::getServerInfo();
            $results['tests']['server_info'] = [
                'status' => $info ? 'PASS' : 'WARN',
                'message' => 'Database server details retrieved',
                'details' => $info ?? ['error' => 'Unable to retrieve server info']
            ];
        }

        // Test 5: UTF8MB4 Support
        if ($results['tests']['connection']['status'] === 'PASS') {
            try {
                $stmt = Database::query('SELECT @@character_set_connection, @@collation_connection');
                $charsetInfo = $stmt->fetch();

                $utf8mb4Valid = str_contains($charsetInfo['@@character_set_connection'] ?? '', 'utf8mb4');

                $results['tests']['charset'] = [
                    'status' => $utf8mb4Valid ? 'PASS' : 'WARN',
                    'message' => $utf8mb4Valid
                        ? 'UTF8MB4 properly configured'
                        : 'Connection charset not UTF8MB4',
                    'details' => $charsetInfo
                ];
            } catch (\Throwable $e) {
                $results['tests']['charset'] = [
                    'status' => 'WARN',
                    'message' => 'Could not verify charset: ' . $e->getMessage(),
                    'details' => []
                ];
            }
        }

        // Test 6: Permission Check (Read Only)
        if ($results['tests']['connection']['status'] === 'PASS') {
            try {
                // Try to read from information_schema (always readable)
                $stmt = Database::query(
                    'SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = ?',
                    [$config['name']]
                );
                $result = $stmt->fetch();

                $results['tests']['read_access'] = [
                    'status' => 'PASS',
                    'message' => 'Read access confirmed',
                    'details' => ['tables_in_database' => (int) ($result['table_count'] ?? 0)]
                ];
            } catch (\Throwable $e) {
                $results['tests']['read_access'] = [
                    'status' => 'FAIL',
                    'message' => 'Cannot read from database: ' . $e->getMessage(),
                    'details' => []
                ];
            }
        }

        return $results;
    }

    /**
     * Display formatted CLI report
     */
    public static function displayCliReport(): void
    {
        $results = self::runAllChecks();

        echo PHP_EOL;
        echo str_repeat('=', self::OUTPUT_WIDTH) . PHP_EOL;
        echo "🔧 POS SYSTEM - DATABASE HEALTH CHECK" . PHP_EOL;
        echo str_repeat('=', self::OUTPUT_WIDTH) . PHP_EOL;
        echo "Timestamp: " . $results['timestamp'] . PHP_EOL;
        echo "Environment: " . $results['environment'] . PHP_EOL;
        echo str_repeat('-', self::OUTPUT_WIDTH) . PHP_EOL . PHP_EOL;

        foreach ($results['tests'] as $testName => $testResult) {
            $statusIcon = match ($testResult['status']) {
                'PASS' => '✅',
                'WARN' => '⚠️ ',
                'FAIL' => '❌',
                default => '❓'
            };

            echo sprintf(
                "%s %s\n",
                $statusIcon,
                strtoupper(str_replace('_', ' ', $testName))
            );
            echo "   └─ " . $testResult['message'] . PHP_EOL;

            if (!empty($testResult['details'])) {
                foreach ($testResult['details'] as $key => $value) {
                    echo "      • {$key}: " . print_r($value, true) . PHP_EOL;
                }
            }
            echo PHP_EOL;
        }

        echo str_repeat('=', self::OUTPUT_WIDTH) . PHP_EOL;

        // Final verdict
        $failCount = count(array_filter($results['tests'], fn($t) => $t['status'] === 'FAIL'));
        $warnCount = count(array_filter($results['tests'], fn($t) => $t['status'] === 'WARN'));

        if ($failCount === 0) {
            echo "✅ DATABASE CONNECTION: OPERATIONAL";
            if ($warnCount > 0) {
                echo " (with {$warnCount} warnings)";
            }
        } else {
            echo "❌ DATABASE CONNECTION: FAILED ({$failCount} critical errors)";
        }
        echo PHP_EOL . str_repeat('=', self::OUTPUT_WIDTH) . PHP_EOL . PHP_EOL;
    }

    /**
     * Load configuration from environment
     */
    private static function getConfigFromEnv(): array
    {
        $envFile = dirname(__DIR__, 2) . '/.env';

        if (!file_exists($envFile)) {
            throw new RuntimeException('.env file not found at: ' . $envFile);
        }

        $config = [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('DB_PORT') ?: '3306',
            'name' => getenv('DB_NAME') ?: '',
            'user' => getenv('DB_USER') ?: '',
            'pass' => getenv('DB_PASS') ?: '',
            'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
        ];

        // Try loading from .env file if getenv failed
        if (empty($config['name']) || empty($config['user'])) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = str_replace('DB_', '', $key);
                    $config[strtolower($key)] = trim($value);
                }
            }
        }

        return $config;
    }
}
