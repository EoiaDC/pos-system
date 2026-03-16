<?php

namespace POS\Controllers\Sales;

use App\Core\Auth;
use App\Core\Response;

class PaymentsController
{
    /**
     * Record a cash payment for a posted sale
     */
    public function record(): void
    {
        // Check login
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Check permission (using DEV D's permission)
        if (!Auth::hasPermission('sales.payments.record')) {
            Response::flash('error', 'You do not have permission to record payments');
            Response::redirect('/sales/draft?sale_id=' . ($_POST['sale_id'] ?? ''));
            return;
        }
        
        // Get and validate inputs
        $saleId = $_POST['sale_id'] ?? '';
        $amount = $_POST['amount'] ?? '';
        $paymentDate = $_POST['payment_date'] ?? $_POST['paid_at'] ?? date('Y-m-d H:i:s');
        $referenceNo = $_POST['reference_no'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        if (!is_numeric($saleId) || $saleId <= 0) {
            Response::flash('error', 'Invalid sale ID');
            Response::redirect('/sales');
            return;
        }
        
        if (!is_numeric($amount) || $amount <= 0) {
            Response::flash('error', 'Amount must be greater than 0');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (strlen($notes) > 255) {
            $notes = substr($notes, 0, 255);
        }
        
        // Get database connection
        $config = require __DIR__ . '/../../../config/database.php';
        $db = new \PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
            $config['user'],
            $config['pass']
        );
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        try {
            $db->beginTransaction();
            
            // Load sale header
            $stmt = $db->prepare("SELECT * FROM sales_headers WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$sale) {
                Response::flash('error', 'Sale not found');
                Response::redirect('/sales');
                return;
            }
            
            // 🔒 LOCK ENFORCEMENT: Check if sale is FINALIZED
            if (strtoupper($sale['status']) === 'FINALIZED') {
                Response::flash('error', 'Cannot add payment to a finalized sale');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Check if sale is POSTED (case insensitive)
            if (strtoupper($sale['status']) !== 'POSTED') {
                Response::flash('error', 'Payments can only be recorded for POSTED sales');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Calculate current paid total and balance using our new table structure
            $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as paid_total FROM payments WHERE sale_id = ?");
            $stmt->execute([$saleId]);
            $paidTotal = $stmt->fetch(\PDO::FETCH_ASSOC)['paid_total'];
            
            $saleTotal = $sale['grand_total'];
            $balance = $saleTotal - $paidTotal;
            
            // Validate no overpayment
            if ($amount > $balance) {
                Response::flash('error', 'Payment amount (₱' . number_format($amount, 2) . 
                              ') exceeds remaining balance (₱' . number_format($balance, 2) . ')');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Insert payment with correct column names
            $stmt = $db->prepare("
                INSERT INTO payments 
                (sale_id, payment_method, amount, payment_date, reference_no, notes, created_by, created_at)
                VALUES (?, 'CASH', ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $saleId,
                $amount,
                $paymentDate,
                $referenceNo,
                $notes ?: null,
                Auth::userId()
            ]);
            
            $paymentId = $db->lastInsertId();
            
            // Audit log
            $userId = Auth::userId() ?? 0;
            $username = Auth::user()['username'] ?? 'system';
            $metadata = json_encode([
                'sale_id' => $saleId,
                'payment_id' => $paymentId,
                'payment_method' => 'CASH',
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'reference_no' => $referenceNo,
                'notes' => $notes,
                'balance_after' => $balance - $amount
            ]);
            
            $stmt = $db->prepare("
                INSERT INTO audit_logs 
                (user_id, username, action, table_name, record_id, new_data, ip_address, user_agent)
                VALUES (?, ?, ?, 'payments', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $username,
                'sale.payment.recorded',
                $paymentId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $db->commit();
            
            $message = 'Payment of ₱' . number_format($amount, 2) . ' recorded. ';
            if ($balance - $amount <= 0) {
                $message .= 'Sale is now fully paid!';
            }
            
            Response::flash('success', $message);
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("PaymentsController::record error: " . $e->getMessage());
            Response::flash('error', 'Failed to record payment: ' . $e->getMessage());
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
}