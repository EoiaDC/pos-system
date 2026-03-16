<?php
// Load bootstrap
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../src/Core/Router.php';

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
    
    header('Location: /pos-system/public/sales');
    exit;
});

// ========== SALES ROUTES ==========

// Sales home
$router->get('/sales', function() {
    require_once __DIR__ . '/../src/Sales/SalesHomeController.php';
    $controller = new POS\Sales\SalesHomeController();
    $controller->index();
});

// Sales history
$router->get('/sales/history', function() {
    require_once __DIR__ . '/../src/Sales/SalesHistoryController.php';
    $controller = new POS\Sales\SalesHistoryController();
    $controller->index();
});


// Register status
$router->get('/sales/register-status', function() {
    require_once __DIR__ . '/../src/Sales/RegisterStatusController.php';
    $controller = new POS\Sales\RegisterStatusController();
    $controller->index();
});

// BIR readiness
$router->get('/sales/bir-readiness', function() {
    require_once __DIR__ . '/../src/Sales/BirReadinessController.php';
    $controller = new POS\Sales\BirReadinessController();
    $controller->index();
});

// START NEW SALE - GET (show form)
$router->get('/sales/start', function() {
    require_once __DIR__ . '/../src/Sales/SaleStartController.php';
    $controller = new POS\Sales\SaleStartController();
    $controller->index();
});

// START NEW SALE - POST (process form)
$router->post('/sales/start', function() {
    require_once __DIR__ . '/../src/Sales/SaleStartController.php';
    $controller = new POS\Sales\SaleStartController();
    $controller->create();
});

// Draft sale
$router->get('/sales/draft', function() {
    require_once __DIR__ . '/../src/Sales/SaleDraftController.php';
    $controller = new POS\Sales\SaleDraftController();
    $controller->index();
});

// Register update
$router->post('/sales/register/update', function() {
    require_once __DIR__ . '/../src/Sales/SaleRegisterController.php';
    $controller = new POS\Sales\SaleRegisterController();
    $controller->update();
});

// OR series update
$router->post('/sales/or-series/update', function() {
    require_once __DIR__ . '/../src/Sales/SaleOrSeriesController.php';
    $controller = new POS\Sales\SaleOrSeriesController();
    $controller->update();
});

// Line add
$router->post('/sales/line/add', function() {
    require_once __DIR__ . '/../src/Sales/SaleLineController.php';
    $controller = new POS\Sales\SaleLineController();
    $controller->add();
});

// Line remove
$router->post('/sales/line/remove', function() {
    require_once __DIR__ . '/../src/Sales/SaleLineController.php';
    $controller = new POS\Sales\SaleLineController();
    $controller->remove();
});

// Post sale
$router->post('/sales/draft/post', function() {
    require_once __DIR__ . '/../src/Sales/SalePostController.php';
    $controller = new POS\Sales\SalePostController();
    $controller->post();
});

// OR issue
$router->post('/sales/or/issue', function() {
    require_once __DIR__ . '/../src/Sales/OrIssueController.php';
    $controller = new POS\Sales\OrIssueController();
    $controller->issue();
});

// Payment record
$router->post('/sales/payments/record', function() {
    require_once __DIR__ . '/../src/Controllers/Sales/PaymentsController.php';
    $controller = new POS\Controllers\Sales\PaymentsController();
    $controller->record();
});

// Root redirect
$router->get('/', function() {
    header('Location: /pos-system/public/sales');
    exit;
});

// //Payment Store
// $router->post('/sales/payment/store', [\POS\Sales\SalesPaymentController::class, 'store'], ['auth' => true]);
// $router->post('/sales/finalize', [\POS\Sales\SaleFinalizationController::class, 'finalize'], ['auth' => true]);

// $router->post('/sales/payment/store', [\POS\Sales\SalesPaymentController::class, 'store'], ['auth' => true, 'perm' => 'sales.payments.record']);
// $router->post('/sales/finalize', [\POS\Sales\SaleFinalizationController::class, 'finalize'], ['auth' => true, 'perm' => 'sales.finalize']);

//Placeholder for Payment
$router->post('/sales/payment/store', function() {
    if (!Auth::check() || !Auth::hasPermission('sales.payments.record')) {
        http_response_code(403);
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
    echo "Payment recording placeholder – controller not yet implemented.";
}, ['auth' => true, 'perm' => 'sales.payments.record']);

//Placeholder for Finalization
$router->post('/sales/finalize', function() {
    if (!Auth::check() || !Auth::hasPermission('sales.finalize')) {
        http_response_code(403);
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
    echo "Finalize placeholder – controller not yet implemented.";
}, ['auth' => true, 'perm' => 'sales.finalize']);

// ========== TEMPORARY ADMIN SETUP PAGES ==========

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
    echo "<p><a href='/pos-system/public/admin/registers'>Next: Create Register →</a></p>";
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
    
    header('Location: /pos-system/public/admin/company-profile?success=1');
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
    echo "<p><a href='/pos-system/public/admin/or-series'>Next: Create OR Series →</a></p>";
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
    
    header('Location: /pos-system/public/admin/registers?success=1');
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
        echo "<p><a href='/pos-system/public/admin/registers'>Go to Registers</a></p>";
    } else {
        echo "<form method='POST'>";
        echo "<p>Series Code: <input type='text' name='series_code' value='OR-2024' required></p>";
        echo "<p>Start Number: <input type='number' name='start_no' value='1000' required></p>";
        echo "<p>End Number: <input type='number' name='end_no' value='9999' required></p>";
        echo "<p><button type='submit'>Create OR Series</button></p>";
        echo "</form>";
    }
    echo "<p><a href='/pos-system/public/sales/start'>Done! Start Sale →</a></p>";
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
    
    header('Location: /pos-system/public/admin/or-series?success=1');
    exit;
});

// ========== DISPATCH ==========

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/pos-system/public';

$path = parse_url($uri, PHP_URL_PATH) ?: '/';
if (str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
    if ($path === '' || $path === false) {
        $path = '/';
    }
}

// DEBUG OUTPUT (now after $path is defined)
echo "<h1>Router Debug</h1>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Base path: " . $base . "\n";
echo "Calculated path: " . $path . "\n";
echo "Method: " . $method . "\n";
echo "</pre>";

$response = $router->dispatch($method, $path);
if ($response === false) {
    http_response_code(404);
    echo "404 - Page not found";
} else {
    echo $response;
}