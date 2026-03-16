<?php
// Load bootstrap
require __DIR__ . '/../src/config/bootstrap.php';

// Define base path
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', '/pos-system/public');
}

use App\Core\Router;

$router = new Router();

// 👇 TEMPORARY LOGIN ROUTE 👇
$router->get('/login', function() {
    include __DIR__ . '/../views/login.php';
});

$router->post('/login', function() {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // For testing, accept any login
    $_SESSION['user'] = ['id' => 1, 'username' => 'admin'];
    
    header('Location: ' . APP_BASE_PATH . '/sales');
    exit;
});

// ========== SALES ROUTES ==========

$router->get('/sales', function() {
    $controller = new POS\Sales\SalesHomeController();
    $controller->index();
});

$router->get('/sales/history', function() {
    $controller = new POS\Sales\SalesHistoryController();
    $controller->index();
});

$router->get('/sales/register-status', function() {
    $controller = new POS\Sales\RegisterStatusController();
    $controller->index();
});

$router->get('/sales/bir-readiness', function() {
    $controller = new POS\Sales\BirReadinessController();
    $controller->index();
});

$router->get('/sales/start', function() {
    $controller = new POS\Sales\SaleStartController();
    $controller->index();
});

$router->post('/sales/start', function() {
    $controller = new POS\Sales\SaleStartController();
    $controller->create();
});

$router->get('/sales/draft', function() {
    $controller = new POS\Sales\SaleDraftController();
    $controller->index();
});

$router->post('/sales/register/update', function() {
    $controller = new POS\Sales\SaleRegisterController();
    $controller->update();
});

$router->post('/sales/or-series/update', function() {
    $controller = new POS\Sales\SaleOrSeriesController();
    $controller->update();
});

$router->post('/sales/line/add', function() {
    $controller = new POS\Sales\SaleLineController();
    $controller->add();
});

$router->post('/sales/line/remove', function() {
    $controller = new POS\Sales\SaleLineController();
    $controller->remove();
});

$router->post('/sales/draft/post', function() {
    $controller = new POS\Sales\SalePostController();
    $controller->post();
});

$router->post('/sales/or/issue', function() {
    $controller = new POS\Sales\OrIssueController();
    $controller->issue();
});

$router->post('/sales/payments/record', function() {
    $controller = new POS\Controllers\Sales\PaymentsController();
    $controller->record();
});

// Root redirect
$router->get('/', function() {
    header('Location: ' . APP_BASE_PATH . '/sales');
    exit;
});

// ========== ADMIN SETUP PAGES ==========

// Company Profile - GET
$router->get('/admin/company-profile', function() {
    $config = require __DIR__ . '/../config/database.php';
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['pass']
    );
    
    $success = isset($_GET['success']);
    
    echo "<h1>Company Profile</h1>";
    if ($success) {
        echo "<p style='color:green'>✅ Company profile saved!</p>";
    }
    echo "<form method='POST'>";
    echo "<p>Company Name: <input type='text' name='company_name' required></p>";
    echo "<p>TIN: <input type='text' name='tin' required></p>";
    echo "<p>Address: <input type='text' name='address' required></p>";
    echo "<p><button type='submit'>Save</button></p>";
    echo "</form>";
    echo "<p><a href='" . APP_BASE_PATH . "/admin/registers'>Next: Create Register →</a></p>";
});

// Company Profile - POST
$router->post('/admin/company-profile', function() {
    $config = require __DIR__ . '/../config/database.php';
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['pass']
    );
    
    $name = $_POST['company_name'] ?? '';
    $tin = $_POST['tin'] ?? '';
    $address = $_POST['address'] ?? '';
    
    $db->exec("DELETE FROM company_profile");
    $stmt = $db->prepare("INSERT INTO company_profile (registered_name, tin, address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$name, $tin, $address]);
    
    header('Location: ' . APP_BASE_PATH . '/admin/company-profile?success=1');
    exit;
});

// Registers - GET
$router->get('/admin/registers', function() {
    $config = require __DIR__ . '/../config/database.php';
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['pass']
    );
    
    $success = isset($_GET['success']);
    
    echo "<h1>POS Registers</h1>";
    if ($success) {
        echo "<p style='color:green'>✅ Register created!</p>";
    }
    echo "<form method='POST'>";
    echo "<p>Register Code: <input type='text' name='register_code' value='REG-001' required></p>";
    echo "<p>Register Name: <input type='text' name='register_name' value='Main Register' required></p>";
    echo "<p><button type='submit'>Create Register</button></p>";
    echo "</form>";
    echo "<p><a href='" . APP_BASE_PATH . "/admin/or-series'>Next: Create OR Series →</a></p>";
});

// Registers - POST
$router->post('/admin/registers', function() {
    $config = require __DIR__ . '/../config/database.php';
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['pass']
    );
    
    $code = $_POST['register_code'] ?? 'REG-001';
    $name = $_POST['register_name'] ?? 'Main Register';
    
    $stmt = $db->prepare("INSERT INTO pos_registers (register_code, machine_name, is_active, created_at) VALUES (?, ?, 1, NOW())");
    $stmt->execute([$code, $name]);
    
    header('Location: ' . APP_BASE_PATH . '/admin/registers?success=1');
    exit;
});

// OR Series - GET
$router->get('/admin/or-series', function() {
    $config = require __DIR__ . '/../config/database.php';
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['pass']
    );
    
    // Get first register ID
    $regId = $db->query("SELECT id FROM pos_registers LIMIT 1")->fetchColumn();
    $success = isset($_GET['success']);
    
    echo "<h1>OR Series</h1>";
    if ($success) {
        echo "<p style='color:green'>✅ OR Series created!</p>";
    }
    if (!$regId) {
        echo "<p style='color:red'>❌ Create a register first!</p>";
        echo "<p><a href='" . APP_BASE_PATH . "/admin/registers'>Go to Registers</a></p>";
    } else {
        echo "<form method='POST'>";
        echo "<p>Series Code: <input type='text' name='series_code' value='OR-2024' required></p>";
        echo "<p>Start Number: <input type='number' name='start_no' value='1000' required></p>";
        echo "<p>End Number: <input type='number' name='end_no' value='9999' required></p>";
        echo "<p><button type='submit'>Create OR Series</button></p>";
        echo "</form>";
    }
    echo "<p><a href='" . APP_BASE_PATH . "/sales/start'>Done! Start Sale →</a></p>";
});

// OR Series - POST
$router->post('/admin/or-series', function() {
    $config = require __DIR__ . '/../config/database.php';
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['pass']
    );
    
    $regId = $db->query("SELECT id FROM pos_registers LIMIT 1")->fetchColumn();
    $code = $_POST['series_code'] ?? 'OR-2024';
    $start = $_POST['start_no'] ?? 1000;
    $end = $_POST['end_no'] ?? 9999;
    
    $stmt = $db->prepare("INSERT INTO or_series (register_id, series_code, start_no, end_no, current_no, is_active, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
    $stmt->execute([$regId, $code, $start, $end, $start]);
    
    header('Location: ' . APP_BASE_PATH . '/admin/or-series?success=1');
    exit;
});

// ========== DISPATCH ==========

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$base = APP_BASE_PATH;

// Calculate path without base
$path = parse_url($uri, PHP_URL_PATH) ?: '/';
if (strpos($path, $base) === 0) {
    $path = substr($path, strlen($base));
}
if (empty($path)) {
    $path = '/';
}

// DEBUG OUTPUT
echo "<h1>Router Debug</h1>";
echo "<div style='background: #f0f0f0; padding: 15px; margin: 20px; border-radius: 5px; font-family: monospace;'>";
echo "<strong>REQUEST_URI:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI']) . "<br>";
echo "<strong>Base path:</strong> " . htmlspecialchars($base) . "<br>";
echo "<strong>Calculated path:</strong> " . htmlspecialchars($path) . "<br>";
echo "<strong>Method:</strong> " . htmlspecialchars($method) . "<br>";
echo "<strong>Routes loaded:</strong> " . (isset($router) ? 'Yes' : 'No') . "<br>";
echo "</div>";

// Dispatch the request
$response = $router->dispatch($method, $path);

if ($response === false) {
    http_response_code(404);
    echo "<div style='background: #f8d7da; padding: 15px; margin: 20px; border-radius: 5px; color: #721c24;'>";
    echo "<strong>404 - Page Not Found</strong><br>";
    echo "No route matched for: " . htmlspecialchars($method) . " " . htmlspecialchars($path);
    echo "</div>";
} else {
    echo $response;
}