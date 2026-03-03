<?php
require __DIR__ . '/config/bootstrap.php';

echo "<h1>Database Configuration Debug</h1>";
echo "<pre>";

echo "ENV variables from getenv():\n";
echo "DB_USERNAME: " . (getenv('DB_USERNAME') ?: 'NOT SET') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: 'NOT SET') . "\n";
echo "DB_DATABASE: " . (getenv('DB_DATABASE') ?: 'NOT SET') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'NOT SET') . "\n\n";

echo "ENV variables from env_get():\n";
echo "env_get('DB_USERNAME'): " . env_get('DB_USERNAME', 'default') . "\n";
echo "env_get('DB_USER'): " . env_get('DB_USER', 'default') . "\n";
echo "env_get('DB_DATABASE'): " . env_get('DB_DATABASE', 'default') . "\n";
echo "env_get('DB_NAME'): " . env_get('DB_NAME', 'default') . "\n\n";

echo "Database config array:\n";
$db = require __DIR__ . '/config/database.php';
print_r($db);

echo "</pre>";
?>