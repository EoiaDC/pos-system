<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../src/Core/Router.php';

use App\Core\Router;

echo "<h1>Sales Routes Test</h1>";

$router = new Router();

// Manually include your sales routes
$routesFile = __DIR__ . '/../src/routes/sales.php';
echo "Looking for routes file: $routesFile<br>";

if (file_exists($routesFile)) {
    echo "✅ Routes file found<br>";
    require_once $routesFile;
    echo "✅ Routes file included<br>";
} else {
    echo "❌ Routes file NOT found<br>";
}

// Test if routes are registered
echo "<h2>Testing routes:</h2>";
echo "<ul>";
echo "<li><a href='/pos-system/public/sales'>/sales</a></li>";
echo "<li><a href='/pos-system/public/sales/start'>/sales/start</a></li>";
echo "<li><a href='/pos-system/public/sales/history'>/sales/history</a></li>";
echo "</ul>";

echo "<p>Click the links above to test each route.</p>";