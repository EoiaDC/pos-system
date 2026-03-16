<?php
require_once 'src/Services/SalePaymentTotalsService.php';

$saleId = 50;

echo "Checking payments for Sale #$saleId\n";
echo "================================\n\n";

$service = new POS\Services\SalePaymentTotalsService();
$totals = $service->getSaleTotals($saleId);

echo "SALE TOTALS:\n";
echo "Total Amount: ₱" . number_format($totals['total_amount'], 2) . "\n";
echo "Total Paid: ₱" . number_format($totals['total_paid'], 2) . "\n";
echo "Balance: ₱" . number_format($totals['balance'], 2) . "\n";
echo "Fully Paid: " . ($totals['is_fully_paid'] ? 'YES' : 'NO') . "\n\n";

// Get database connection directly to check payments table
$config = require __DIR__ . '/config/database.php';
$db = new PDO(
    "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
    $config['user'],
    $config['pass']
);

$stmt = $db->prepare("SELECT * FROM payments WHERE sale_id = ? ORDER BY created_at DESC");
$stmt->execute([$saleId]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "PAYMENTS RECORDED:\n";
if (empty($payments)) {
    echo "No payments found for sale #$saleId\n";
} else {
    foreach ($payments as $index => $payment) {
        echo ($index + 1) . ". Amount: ₱" . number_format($payment['amount'], 2) . "\n";
        echo "   Date: " . $payment['payment_date'] . "\n";
        echo "   Method: " . $payment['payment_method'] . "\n";
        echo "   Reference: " . ($payment['reference_no'] ?? 'N/A') . "\n";
        echo "   Notes: " . ($payment['notes'] ?? 'N/A') . "\n\n";
    }
}
