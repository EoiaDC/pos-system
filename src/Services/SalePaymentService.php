<?php
/**
 * Sale Payment Service
 * 
 * Handles recording payments for sales with validation and audit logging
 * 
 * @package POS\Services
 */

namespace POS\Services;

use PDO;
use POS\Services\SalePaymentTotalsService;

class SalePaymentService
{
    private $db;
    private $totalsService;
    
    public function __construct()
    {
        // Get database connection
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
            $config['user'],
            $config['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        $this->totalsService = new SalePaymentTotalsService();
    }
    
    /**
     * Record a payment for a sale
     * 
     * @param int $saleId
     * @param array $data Payment data
     * @param int $userId User recording the payment
     * @return array
     * @throws \Exception
     */
    public function recordPayment(int $saleId, array $data, int $userId): array
    {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Validate sale exists and is in correct state
            $sale = $this->validateSaleForPayment($saleId);
            
            // Validate payment data
            $this->validatePaymentData($data);
            
            // Check if sale is already fully paid
            if ($this->totalsService->isFullyPaid($saleId)) {
                throw new \Exception('Sale is already fully paid');
            }
            
            // Get current totals to check if payment exceeds balance
            $totals = $this->totalsService->getSaleTotals($saleId);
            $amount = (float)$data['amount'];
            
            if ($amount > $totals['balance']) {
                throw new \Exception(
                    'Payment amount (₱' . number_format($amount, 2) . 
                    ') exceeds remaining balance (₱' . number_format($totals['balance'], 2) . ')'
                );
            }
            
            // Insert payment
            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    sale_id, payment_date, payment_method, 
                    reference_no, amount, notes, created_by, created_at
                ) VALUES (
                    :sale_id, :payment_date, :payment_method,
                    :reference_no, :amount, :notes, :created_by, NOW()
                )
            ");
            
            $stmt->execute([
                'sale_id' => $saleId,
                'payment_date' => $data['payment_date'] ?? date('Y-m-d H:i:s'),
                'payment_method' => $data['payment_method'] ?? 'CASH',
                'reference_no' => $data['reference_no'] ?? null,
                'amount' => $amount,
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId
            ]);
            
            $paymentId = $this->db->lastInsertId();
            
            // Get updated totals after payment
            $newTotals = $this->totalsService->getSaleTotals($saleId);
            
            // Create audit log
            $this->createAuditLog($saleId, $paymentId, $amount, $data, $userId, $newTotals);
            
            $this->db->commit();
            
            return [
                'payment_id' => (int)$paymentId,
                'sale_id' => $saleId,
                'amount' => $amount,
                'total_paid' => $newTotals['total_paid'],
                'balance' => $newTotals['balance'],
                'is_fully_paid' => $newTotals['is_fully_paid']
            ];
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Validate sale exists and is in correct state for payment
     * 
     * @param int $saleId
     * @return array
     * @throws \Exception
     */
    private function validateSaleForPayment(int $saleId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, status, grand_total 
            FROM sales_headers 
            WHERE id = ?
        ");
        $stmt->execute([$saleId]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sale) {
            throw new \Exception('Sale not found');
        }
        
        // Check if already finalized
        if ($sale['status'] === 'FINALIZED') {
            throw new \Exception('Cannot add payment to finalized sale');
        }
        
        // Must be POSTED (not DRAFT) - case insensitive
        if (strtoupper($sale['status']) !== 'POSTED') {
            throw new \Exception('Sale must be POSTED to add payments (current status: ' . $sale['status'] . ')');
        }
        
        return $sale;
    }
    
    /**
     * Validate payment data
     * 
     * @param array $data
     * @throws \Exception
     */
    private function validatePaymentData(array $data): void
    {
        $errors = [];
        
        // Amount validation
        if (!isset($data['amount'])) {
            $errors[] = 'Amount is required';
        } elseif (!is_numeric($data['amount'])) {
            $errors[] = 'Amount must be a number';
        } elseif ($data['amount'] <= 0) {
            $errors[] = 'Amount must be greater than zero';
        }
        
        // Payment method validation (Week 1: CASH only)
        $method = $data['payment_method'] ?? 'CASH';
        if ($method !== 'CASH') {
            $errors[] = 'Only CASH payments are allowed in Week 1';
        }
        
        // Payment date validation
        if (empty($data['payment_date'])) {
            $errors[] = 'Payment date is required';
        } else {
            // Check if date is valid
            $timestamp = strtotime($data['payment_date']);
            if ($timestamp === false) {
                $errors[] = 'Invalid payment date format';
            }
        }
        
        if (!empty($errors)) {
            throw new \Exception('Validation failed: ' . implode(', ', $errors));
        }
    }
    
    /**
     * Create audit log entry for payment
     * 
     * @param int $saleId
     * @param int $paymentId
     * @param float $amount
     * @param array $data
     * @param int $userId
     * @param array $totals
     */
    private function createAuditLog(int $saleId, int $paymentId, float $amount, array $data, int $userId, array $totals): void
    {
        // Check if audit_logs table exists and has the expected structure
        try {
            $payload = [
                'payment_id' => $paymentId,
                'sale_id' => $saleId,
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'CASH',
                'payment_date' => $data['payment_date'] ?? date('Y-m-d H:i:s'),
                'reference_no' => $data['reference_no'] ?? null,
                'total_paid' => $totals['total_paid'],
                'balance' => $totals['balance'],
                'is_fully_paid' => $totals['is_fully_paid']
            ];
            
            // Try to insert into audit_logs if table exists
            $stmt = $this->db->prepare("
                INSERT INTO audit_logs 
                (table_name, record_id, action, old_data, new_data, created_by, created_at)
                VALUES ('payments', ?, 'sale.payment.recorded', NULL, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $paymentId,
                json_encode($payload),
                $userId
            ]);
            
        } catch (\Exception $e) {
            // If audit_logs table doesn't exist, just log to error log
            error_log('Audit log failed (table may not exist): ' . $e->getMessage());
            error_log('Payment recorded: ' . json_encode($payload));
        }
    }
    
    /**
     * Get payments for a sale
     * 
     * @param int $saleId
     * @return array
     */
    public function getSalePayments(int $saleId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                u.username as created_by_username
            FROM payments p
            LEFT JOIN users u ON p.created_by = u.id
            WHERE p.sale_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$saleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}