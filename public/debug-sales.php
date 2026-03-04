<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../src/Core/Router.php';

use App\Core\Router;

echo "<h1>🔍 Sales Route Debugger</h1>";

$router = new Router();

// Include your routes file
require_once __DIR__ . '/../src/routes/sales.php';

echo "<h2>Route Registration Complete</h2>";

// Get current request info
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/pos-system/public';

echo "<h3>Request Information:</h3>";
echo "<pre>";
echo "URI: $uri\n";
echo "Method: $method\n";
echo "Base path: $base\n";

// Calculate route path
$path = parse_url($uri, PHP_URL_PATH) ?: '/';
echo "Full path: $path\n";

if (str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
    if ($path === '' || $path === false) {
        $path = '/';
    }
}
echo "Route path: $path\n";
echo "</pre>";

// Try to dispatch
echo "<h3>Dispatching...</h3>";
$response = $router->dispatch($method, $path);

if ($response === false) {
    echo "<p style='color:red'>❌ No route matched for path: $path</p>";
    
    // List all registered routes (if your router has a way to list them)
    echo "<p>Try accessing one of these URLs directly:</p>";
    echo "<ul>";
    echo "<li><a href='/pos-system/public/sales'>/pos-system/public/sales</a></li>";
    echo "<li><a href='/pos-system/sales'>/pos-system/sales</a></li>";
    echo "<li><a href='/sales'>/sales</a></li>";
    echo "</ul>";
} else {
    echo "<p style='color:green'>✅ Route matched!</p>";
    echo $response;
}