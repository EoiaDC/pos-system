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
        
        // Get and validate inputs
        $saleId = $_POST['sale_id'] ?? '';
        $amount = $_POST['amount'] ?? '';
        $paidAt = $_POST['paid_at'] ?? date('Y-m-d H:i:s');
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
            
            // Check if sale is POSTED
            if ($sale['status'] !== 'POSTED') {
                Response::flash('error', 'Payments can only be recorded for POSTED sales');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Calculate current paid total and balance
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
            
            // Insert payment
            $stmt = $db->prepare("
                INSERT INTO payments 
                (sale_id, method, amount, paid_at, notes, created_by, created_at)
                VALUES (?, 'CASH', ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $saleId,
                $amount,
                $paidAt,
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
                'method' => 'CASH',
                'amount' => $amount,
                'paid_at' => $paidAt,
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
            
            Response::flash('success', 'Payment of ₱' . number_format($amount, 2) . ' recorded');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("PaymentsController::record error: " . $e->getMessage());
            Response::flash('error', 'Failed to record payment');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
}