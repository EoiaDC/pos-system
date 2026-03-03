<?php
require __DIR__ . '/config/bootstrap.php';

echo "<h1>Path Debug</h1>";
echo "<pre>";
echo "BASE_PATH: " . BASE_PATH . "\n";
echo "Full .env path: " . BASE_PATH . DIRECTORY_SEPARATOR . '.env' . "\n";
echo "File exists? " . (file_exists(BASE_PATH . DIRECTORY_SEPARATOR . '.env') ? 'YES' : 'NO') . "\n";
echo "</pre>";
?>