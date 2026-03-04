<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../src/Core/Router.php';

use App\Core\Router;

echo "<h1>Router Path Test</h1>";

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/pos-system/public';

echo "<h2>Debug Info:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $uri . "\n";
echo "APP_BASE_PATH: " . $base . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";

// Test router
$router = new Router();

// Add a test route
$router->get('/test', function() {
    return "✅ Test route matched!";
});

// Add a test route for sales
$router->get('/sales', function() {
    return "✅ Sales route matched!";
});

// Dispatch
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

// Remove base path
if (str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
    if ($path === '' || $path === false) {
        $path = '/';
    }
}

echo "Path after base removal: " . $path . "\n";
echo "</pre>";

// Try to dispatch
$response = $router->dispatch($method, $path);
if ($response === false) {
    echo "<p style='color:red'>❌ No route matched for path: $path</p>";
    
    // List all registered routes (if your Router has a method for this)
    echo "<h3>Available routes:</h3>";
    echo "<ul>";
    echo "<li>GET /test</li>";
    echo "<li>GET /sales</li>";
    echo "</ul>";
} else {
    echo "<p style='color:green'>✅ Route matched!</p>";
    echo $response;
}