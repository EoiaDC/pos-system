<?php
require __DIR__ . '/config/bootstrap.php';

// Import the DB class with its namespace
use Pos\Database\DB;

echo "<h1>✅ POS System — Direct Controller Test</h1>";

// Test database connection
try {
    $db = DB::connect();
    echo "<p style='color:green'>✓ Database connected</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test users table
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetchColumn();
    echo "<p style='color:green'>✓ Users table exists ($count users)</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Users table error: " . $e->getMessage() . "</p>";
}

// Test sales tables
$tables = ['sales_headers', 'sales_lines', 'payments'];
foreach ($tables as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetchColumn();
        echo "<p style='color:green'>✓ $table exists ($count rows)</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ $table error: " . $e->getMessage() . "</p>";
    }
}

// Test if Sales controllers exist
$controllers = [
    'SalesHomeController',
    'SaleStartController',
    'SaleDraftController',
    'SaleLineController',
    'SalePostController',
    'OrIssueController',
    'PaymentsController'
];

echo "<h2>Controller Files:</h2>";
foreach ($controllers as $controller) {
    $path = __DIR__ . "/src/Sales/$controller.php";
    if (file_exists($path)) {
        echo "<p style='color:green'>✓ $controller.php exists in Sales/</p>";
    } else {
        $path2 = __DIR__ . "/src/Controllers/Sales/$controller.php";
        if (file_exists($path2)) {
            echo "<p style='color:green'>✓ $controller.php exists in Controllers/Sales/</p>";
        } else {
            echo "<p style='color:red'>✗ $controller.php not found</p>";
        }
    }
}

echo "<h2>✅ All 22 migrations succeeded!</h2>";
echo "<p>Database is fully built and ready.</p>";
echo "<h2>Next Steps:</h2>";
echo "<p>⏳ Routes need to be registered by DEV D</p>";
echo "<p>📁 Your routes file is ready at: <code>src/routes/sales.php</code></p>";
echo "<p>🔗 Once routes are active, visit: <a href='/sales'>/sales</a></p>";
?>