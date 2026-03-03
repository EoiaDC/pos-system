<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config/bootstrap.php';

echo "<h1>ENV Debug Viewer</h1>";

echo "<h2>1. Current ENV variables from getenv():</h2>";
echo "<pre>";
$vars = ['DB_USERNAME', 'DB_USER', 'DB_DATABASE', 'DB_NAME', 'DB_HOST', 'DB_PASSWORD'];
foreach ($vars as $var) {
    $val = getenv($var);
    echo $var . ": " . ($val ? $val : 'NOT SET') . "\n";
}
echo "</pre>";

echo "<h2>2. Current ENV variables from env_get():</h2>";
echo "<pre>";
foreach ($vars as $var) {
    $val = env_get($var, 'DEFAULT');
    if (strpos($var, 'PASS') !== false) {
        echo $var . ": ********\n";
    } else {
        echo $var . ": " . $val . "\n";
    }
}
echo "</pre>";

echo "<h2>3. Looking for .env file:</h2>";
echo "<pre>";
$paths = [
    __DIR__ . '/.env',
    __DIR__ . '/../.env',
    'C:\\xampp\\htdocs\\pos-system\\.env',
    'C:\\xampp\\htdocs\\.env'
];

foreach ($paths as $path) {
    echo "Checking: " . $path . "\n";
    if (file_exists($path)) {
        echo "FOUND!\n";
        echo "Contents:\n";
        echo file_get_contents($path);
        echo "\n---\n";
    } else {
        echo "Not found\n";
    }
}
echo "</pre>";

echo "<h2>4. Database config array:</h2>";
echo "<pre>";
$db = require __DIR__ . '/config/database.php';
print_r($db);
echo "</pre>";
?>