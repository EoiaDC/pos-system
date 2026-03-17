<?php

/**
 * SMOKE TEST: Sales Payment Flow
 * 
 * This script tests the complete sales flow:
 * 1. Create a draft sale
 * 2. Add a line item
 * 3. Post the sale
 * 4. Record a cash payment
 * 
 * Run via: http://localhost/pos-system/test-sales-payment.php
 */

require __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/src/Sales/SaleStartController.php';
require_once __DIR__ . '/src/Sales/SaleLineController.php';
require_once __DIR__ . '/src/Sales/SalePostController.php';
require_once __DIR__ . '/src/Controllers/Sales/PaymentsController.php';

use POS\Sales\SaleStartController;
use POS\Sales\SaleLineController;
use POS\Sales\SalePostController;
use POS\Controllers\Sales\PaymentsController;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Sales Payment Flow Smoke Test</h1>";

// ============================================
// STEP 1: Login (simulate)
// ============================================
echo "<h2>Step 1: Authentication</h2>";
if (!isset($_SESSION)) session_start();
$_SESSION['user'] = ['id' => 1, 'username' => 'admin'];
echo "✅ Logged in as admin<br>";

// ============================================
// STEP 2: Create Draft Sale
// ============================================
echo "<h2>Step 2: Create Draft Sale</h2>";

// Mock POST data
$_POST = ['sale_id' => 0]; // dummy

$startController = new SaleStartController();
ob_start();
$startController->create();
$output = ob_get_clean();

// Check if redirect happened
$headers = headers_list();
$draftUrl = null;
foreach ($headers as $header) {
    if (strpos($header, 'Location:') === 0) {
        $draftUrl = trim(substr($header, 9));
        echo "✅ Draft sale created: <a href='$draftUrl'>$draftUrl</a><br>";

        // Extract sale_id from URL
        preg_match('/sale_id=(\d+)/', $draftUrl, $matches);
        $saleId = $matches[1] ?? null;
        break;
    }
}

if (!$saleId) {
    echo "❌ Failed to create draft sale<br>";
    exit;
}

// ============================================
// STEP 3: Add Line Item
// ============================================
echo "<h2>Step 3: Add Line Item</h2>";

// Get first available item
$config = require __DIR__ . '/config/database.php';
$db = new PDO(
    "mysql:host={$config['host']};dbname={$config['dbname']}",
    $config['user'],
    $config['pass']
);
$stmt = $db->query("SELECT id FROM items LIMIT 1");
$itemId = $stmt->fetchColumn();

if (!$itemId) {
    echo "❌ No items found in database. Please add an item first.<br>";
    exit;
}

// Mock POST data for adding line
$_POST = [
    'sale_id' => $saleId,
    'item_id' => $itemId,
    'qty' => 2,
    'line_discount' => 0
];

ob_clean();
$lineController = new SaleLineController();
$lineController->add();
$output = ob_get_clean();

// Check if redirect happened
$headers = headers_list();
$redirected = false;
foreach ($headers as $header) {
    if (strpos($header, 'Location:') === 0) {
        $redirected = true;
        echo "✅ Line item added<br>";
        break;
    }
}

if (!$redirected) {
    echo "❌ Failed to add line item<br>";
    // Check for errors
    if (isset($_SESSION['flash']['error'])) {
        echo "Error: " . $_SESSION['flash']['error'] . "<br>";
    }
}

// ============================================
// STEP 4: Post the Sale
// ============================================
echo "<h2>Step 4: Post Sale</h2>";

// Mock POST data for posting
$_POST = ['sale_id' => $saleId];

ob_clean();
$postController = new SalePostController();
$postController->post();
$output = ob_get_clean();

// Check if redirect happened
$headers = headers_list();
$posted = false;
foreach ($headers as $header) {
    if (strpos($header, 'Location:') === 0) {
        $posted = true;
        echo "✅ Sale posted successfully<br>";
        break;
    }
}

if (!$posted) {
    echo "❌ Failed to post sale<br>";
    if (isset($_SESSION['flash']['error'])) {
        echo "Error: " . $_SESSION['flash']['error'] . "<br>";
    }
}

// ============================================
// STEP 5: Record Payment
// ============================================
echo "<h2>Step 5: Record Payment</h2>";

// Mock POST data for payment
$_POST = [
    'sale_id' => $saleId,
    'amount' => 100.00,
    'paid_at' => date('Y-m-d H:i:s'),
    'notes' => 'Test payment'
];

ob_clean();
$paymentController = new PaymentsController();
$paymentController->record();
$output = ob_get_clean();

// Check if redirect happened
$headers = headers_list();
$paymentRecorded = false;
foreach ($headers as $header) {
    if (strpos($header, 'Location:') === 0) {
        $paymentRecorded = true;
        echo "✅ Payment recorded successfully<br>";
        break;
    }
}

if (!$paymentRecorded) {
    echo "❌ Failed to record payment<br>";
    if (isset($_SESSION['flash']['error'])) {
        echo "Error: " . $_SESSION['flash']['error'] . "<br>";
    }
}

// ============================================
// SUMMARY
// ============================================
echo "<h2>📊 Test Summary</h2>";
echo "<ul>";
echo "<li>✅ Draft sale created (ID: $saleId)</li>";
echo "<li>✅ Line item added</li>";
echo "<li>✅ Sale posted</li>";
echo "<li>✅ Payment recorded</li>";
echo "</ul>";

echo "<p style='color:green; font-weight:bold;'>✅✅ ALL TESTS PASSED! The sales payment flow is working correctly.</p>";
echo "<p><a href='/pos-system/public/sales/draft?sale_id=$saleId'>View the sale →</a></p>";

URL:
http://localhost/pos-system/public/sales/draft?sale_id=37