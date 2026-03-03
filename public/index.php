<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        header('Content-Type: text/plain');
        echo "FATAL ERROR: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'];
    }
});

use App\Admin\AdminHomeController;
use App\Admin\UsersController;
use App\Admin\UserRolesController;
use App\Admin\RolesController;
use App\Admin\RolePermissionsController;
use App\Admin\CompanyProfileController;
use App\Admin\RegistersController;
use App\Admin\OrSeriesController;
use \App\Admin\BirReadinessController;
use App\Core\SalesDraftService;
use App\Sales\SaleDraftController;
use App\Sales\SetupController;
use App\Core\User\SalesLinesController;
use App\Audit\AuditEvent;
use App\Audit\Auditor;

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../src/Core/Router.php';
require __DIR__ . '/../src/Core/View.php';
require __DIR__ . '/../src/Auth/Auth.php';
require __DIR__ . '/../src/Auth/Rbac.php';
require __DIR__ . '/../src/Audit/AuditEvent.php';
require __DIR__ . '/../src/Audit/Auditor.php';

// Import namespaced classes
use App\Core\Router;
use App\Core\View;

session_start();

$router = new Router(); // Now works
// ... rest of your code unchanged

// GET routes
$router->get('/', function () {
    return View::render('home');
});

$router->get('/login', function () {
    return View::render('login');
});

// POST routes
$router->post('/login', function () {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        flash('error', 'Username and password required.');
        header('Location: ' . APP_BASE_PATH . '/login');
        exit;
    }

    if (Auth::attempt($username, $password)) {
        // Record login audit
        $event = new AuditEvent('LOGIN', 'user');
        $event->actor_user_id = $_SESSION['user']['id'];
        Auditor::record($event);

        flash('success', 'Welcome back, ' . $_SESSION['user']['full_name'] . '!');
        header('Location: ' . APP_BASE_PATH . '/');
        exit;
    } else {
        flash('error', 'Invalid username or password.');
        header('Location: ' . APP_BASE_PATH . '/login');
        exit;
    }
});

$router->post('/logout', function () {
    if (Auth::check()) {
        // Record logout audit before destroying session
        $event = new AuditEvent('LOGOUT', 'user');
        $event->actor_user_id = $_SESSION['user']['id'];
        Auditor::record($event);
    }
    Auth::logout();
    flash('info', 'You have been logged out.');
    header('Location: ' . APP_BASE_PATH . '/login');
    exit;
});

// ========== PART 7: Module routes with guards ==========
$router->get('/sales', function() {
    $html = "<h1>Sales Module</h1><p>Placeholder for sales dashboard.</p>";
    if (Auth::hasPermission('sales.create')) {
        $html .= '<p><a href="' . APP_BASE_PATH . '/sales/start">Start New Sale</a></p>';
    }
    return $html;
}, ['auth' => true, 'perm' => 'sales.view']);

$router->get('/inventory', function() {
    return "<h1>Inventory Module</h1><p>Placeholder for inventory dashboard.</p>";
}, ['auth' => true, 'perm' => 'inventory.view']);

$router->get('/purchasing', function() {
    return "<h1>Purchasing Module</h1><p>Placeholder for purchasing dashboard.</p>";
}, ['auth' => true, 'perm' => 'purchasing.view']);

// Admin dashboard
$router->get('/admin', [AdminHomeController::class, 'index'], ['auth' => true, 'perm' => 'admin.access']);

// User management
$router->get('/admin/users', [UsersController::class, 'index'], ['auth' => true, 'perm' => 'admin.users.manage']);
$router->get('/admin/user-roles', [UserRolesController::class, 'index'], ['auth' => true, 'perm' => 'admin.users.manage']);
$router->post('/admin/user-roles', [UserRolesController::class, 'update'], ['auth' => true, 'perm' => 'admin.users.manage']);

// Role management (read-only)
$router->get('/admin/roles', [RolesController::class, 'index'], ['auth' => true, 'perm' => 'admin.roles.manage']);
$router->get('/admin/role-permissions', [RolePermissionsController::class, 'index'], ['auth' => true, 'perm' => 'admin.roles.manage']);

// Additional module routes for Dev A/B/C (placeholders for now)
$router->get('/sales/new', function() { return "<h1>New Sale</h1>"; }, ['auth'=>true, 'perm'=>'sales.create']);
$router->get('/sales/history', function() { return "<h1>Sales History</h1>"; }, ['auth'=>true, 'perm'=>'sales.view']);
$router->get('/inventory/items', function() { return "<h1>Items</h1>"; }, ['auth'=>true, 'perm'=>'inventory.items.manage']);
$router->get('/inventory/categories', function() { return "<h1>Categories</h1>"; }, ['auth'=>true, 'perm'=>'inventory.categories.manage']);
$router->get('/inventory/uom', function() { return "<h1>UOM</h1>"; }, ['auth'=>true, 'perm'=>'inventory.uom.manage']);
$router->get('/purchasing/suppliers', function() { return "<h1>Suppliers</h1>"; }, ['auth'=>true, 'perm'=>'purchasing.suppliers.manage']);
$router->get('/purchasing/purchase-orders', function() { return "<h1>Purchase Orders</h1>"; }, ['auth'=>true, 'perm'=>'purchasing.view']);

// BIR Company Profile
$router->get('/admin/company-profile', [CompanyProfileController::class, 'index'], ['auth' => true, 'perm' => 'bir.company_profile.manage']);
$router->post('/admin/company-profile', [CompanyProfileController::class, 'update'], ['auth' => true, 'perm' => 'bir.company_profile.manage']);

// POS Registers
$router->get('/admin/registers', [RegistersController::class, 'index'], ['auth' => true, 'perm' => 'bir.registers.manage']);
$router->post('/admin/registers/create', [RegistersController::class, 'create'], ['auth' => true, 'perm' => 'bir.registers.manage']);
$router->post('/admin/registers/toggle', [RegistersController::class, 'toggle'], ['auth' => true, 'perm' => 'bir.registers.manage']);

// OR Series
$router->get('/admin/or-series', [OrSeriesController::class, 'index'], ['auth' => true, 'perm' => 'bir.or_series.manage']);
$router->post('/admin/or-series/create', [OrSeriesController::class, 'create'], ['auth' => true, 'perm' => 'bir.or_series.manage']);

// Module placeholders for other devs (optional)
$router->get('/sales/register-status', function() { return "<h1>Register Status</h1><p>Placeholder for sales module.</p>"; }, ['auth' => true, 'perm' => 'sales.view']);
$router->get('/inventory/stock-movement', function() { return "<h1>Stock Movement</h1><p>Placeholder for inventory module.</p>"; }, ['auth' => true, 'perm' => 'inventory.view']);
$router->get('/purchasing/receiving', function() { return "<h1>Receiving</h1><p>Placeholder for purchasing module.</p>"; }, ['auth' => true, 'perm' => 'purchasing.view']);

$router->get('/admin/bir-readiness', [BirReadinessController::class, 'index'], ['auth' => true, 'perm' => 'bir.readiness.view']);

// GET /sales/start – show a simple start form (or just a button)
$router->get('/sales/start', function() {
    if (!Auth::check()) {
        header('Location: ' . APP_BASE_PATH . '/login');
        exit;
    }
    if (!Auth::hasPermission('sales.create')) {
        http_response_code(403);
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
    // Simple form with just a submit button
    return '
        <h1>Start New Sale</h1>
        <form method="POST" action="' . APP_BASE_PATH . '/sales/start">
            <button type="submit">Create Draft Sale</button>
        </form>
        <p><a href="' . APP_BASE_PATH . '/">Back</a></p>
    ';
}, ['auth' => true, 'perm' => 'sales.create']);  // auth+perm already checked, but double-check in closure

// POST /sales/start – create draft and redirect
$router->post('/sales/start', function() {
    if (!Auth::check()) {
        header('Location: ' . APP_BASE_PATH . '/login');
        exit;
    }
    if (!Auth::hasPermission('sales.create')) {
        http_response_code(403);
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }

    $saleId = SalesDraftService::createDraft(Auth::userId());
    if ($saleId === 0) {
        // Not ready
        flash('error', 'BIR readiness incomplete. Complete setup before selling.');
        header('Location: ' . APP_BASE_PATH . '/admin/bir-readiness');
        exit;
    }

    // Redirect to draft page (DEV A will handle this route later)
    // For now, just show a placeholder
    flash('success', "Draft sale created (ID: $saleId)");
    header('Location: ' . APP_BASE_PATH . '/');
    exit;
}, ['auth' => true, 'perm' => 'sales.create']);

// If DEV A creates a /sales/draft route later, add it here as well
//PLACEHOLDER: This route is just for testing the redirect after creating a draft sale. DEV A will implement the actual draft sale page later.
$router->get('/sales/draft', function() {
    $saleId = $_GET['sale_id'] ?? 0;
    return "<h1>Draft Sale (Placeholder)</h1><p>Sale ID: $saleId</p>";
}, ['auth' => true, 'perm' => 'sales.view']);

//PLACEHOLDER: These routes are for testing the draft sale setup process. DEV A will implement the actual logic later.
$router->post('/sales/draft/set-register', function() {
    http_response_code(501);
    echo "SaleSetupController not implemented yet.";
    exit;
}, ['auth' => true, 'perm' => 'sales.create']);

$router->post('/sales/draft/set-or-series', function() {
    http_response_code(501);
    echo "SaleSetupController not implemented yet.";
    exit;
}, ['auth' => true, 'perm' => 'sales.create']);

//Temporary Test Route for Auditing (can be removed later)
$router->get('/test-audit', function() {
    Auditor::log('sale.started', [
        'entity_id' => 999,
        'meta' => ['note' => 'test from DEV D']
    ]);
    return "Audit test completed. Check audit_logs table.";
}, ['auth' => true, 'perm' => 'admin.access']); // only admin can access

// // Add line to draft
// $router->post('/sales/draft/add-line', [SaleLinesController::class, 'addLine'], ['auth' => true, 'perm' => 'sales.create']);

// // Remove line from draft
// $router->post('/sales/draft/remove-line', [SaleLinesController::class, 'removeLine'], ['auth' => true, 'perm' => 'sales.create']);

// $router->get('/sales/draft', [SaleDraftController::class, 'index'], ['auth' => true, 'perm' => 'sales.view']);

// Temporary placeholders until DEV A implements controllers
$router->post('/sales/draft/add-line', function() {
    http_response_code(501);
    echo "Add line endpoint - not implemented yet (DEV A's task).";
    exit;
}, ['auth' => true, 'perm' => 'sales.create']);

$router->post('/sales/draft/remove-line', function() {
    http_response_code(501);
    echo "Remove line endpoint - not implemented yet (DEV A's task).";
    exit;
}, ['auth' => true, 'perm' => 'sales.create']);

$router->get('/sales/draft', function() {
    $saleId = $_GET['sale_id'] ?? 0;
    echo "<h1>Draft Sale Page (Placeholder)</h1>";
    echo "<p>Sale ID: " . htmlspecialchars($saleId) . "</p>";
    echo "<p>This will be replaced by DEV A's controller.</p>";
}, ['auth' => true, 'perm' => 'sales.view']);


// //Temporary Test Route for SalesTotalsService (can be removed later)
// $router->get('/test-recompute', function() {
//     $saleId = $_GET['sale_id'] ?? 0;
//     if (!$saleId) {
//         return "Missing sale_id";
//     }
//     try {
//         SalesTotalsService::recompute($saleId);
//         return "Recompute executed for sale $saleId";
//     } catch (\Exception $e) {
//         return "Error: " . $e->getMessage();
//     }
// }, ['auth' => true, 'perm' => 'admin.access']); // only admin can access

// //test route for SalesTotalsService with fully qualified class name to avoid any issues with imports. This can be removed later.
// $router->get('/test-recompute', function() {
//     $saleId = $_GET['sale_id'] ?? 0;
//     if (!$saleId) {
//         return "Missing sale_id";
//     }
//     try {
//         \App\Core\SalesTotalsService::recompute($saleId);
//         return "Recompute executed for sale $saleId";
//     } catch (\Exception $e) {
//         return "Error: " . $e->getMessage();
//     }
// }, ['auth' => true, 'perm' => 'admin.access']);

// Temporary placeholder – remove when DEV A implements SalePostController
$router->post('/sales/draft/post', function() {
    http_response_code(501);
    echo json_encode(['error' => 'SalePostController not implemented yet (DEV A\'s task).']);
    exit;
}, ['auth' => true, 'perm' => 'sales.create']);

// Temporary placeholder – remove when DEV A implements OrIssueController
$router->post('/sales/or/issue', function() {
    http_response_code(501);
    echo json_encode(['error' => 'OrIssueController not implemented yet (DEV A\'s task).']);
    exit;
}, ['auth' => true, 'perm' => 'sales.create']);

// // TEMPORARY TEST ROUTE – remove after testing
// $router->post('/test-issue-or', function() {
//     $saleId = $_POST['sale_id'] ?? 0;
//     if (!$saleId) {
//         return "Missing sale_id";
//     }
//     $result = \App\Core\OrIssuanceService::issueForSale((int)$saleId, Auth::userId());
//     if ($result > 0) {
//         return "OR issued: $result";
//     } else {
//         return "Failed to issue OR";
//     }
// }, ['auth' => true, 'perm' => 'admin.access']); // admin only









// Dispatch
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

$base = APP_BASE_PATH;
if (str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
    if ($path === '') {
        $path = '/';
    }
}

$response = $router->dispatch($method, $path);
if ($response === false) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
} else {
    echo $response;
}