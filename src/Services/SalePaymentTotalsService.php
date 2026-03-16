<?php
/**
 * Sale Payment Totals Service
 * 
 * Computes paid amounts and balances for sales
 * Used throughout the payment flow to check payment status
 * 
 * @package POS\Services
 */

namespace POS\Services;

use PDO;
use POS\Helpers\Database;

class SalePaymentTotalsService
{
    private $db;
    
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
    }
    
    /**
     * Get complete payment totals for a sale
     * 
     * @param int $saleId
     * @return array
     */
    public function getSaleTotals(int $saleId): array
    {
        // Get sale total amount from sales_headers
        $stmt = $this->db->prepare("
            SELECT 
                id,
                grand_total as total_amount,
                subtotal
            FROM sales_headers 
            WHERE id = ?
        ");
        $stmt->execute([$saleId]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sale) {
            return [
                'sale_id' => $saleId,
                'total_amount' => 0,
                'total_paid' => 0,
                'balance' => 0,
                'is_fully_paid' => false
            ];
        }
        
        // Get total paid from payments table
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_paid
            FROM payments 
            WHERE sale_id = ?
        ");
        $stmt->execute([$saleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalPaid = (float)($result['total_paid'] ?? 0);
        
        $totalAmount = (float)($sale['total_amount'] ?? 0);
        $balance = $totalAmount - $totalPaid;
        
        // Use small epsilon for floating point comparison
        $isFullyPaid = ($balance <= 0.001);
        
        return [
            'sale_id' => (int)$saleId,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'balance' => max(0, $balance), // Never return negative balance
            'is_fully_paid' => $isFullyPaid
        ];
    }
    
    /**
     * Get total paid amount for a sale
     * 
     * @param int $saleId
     * @return float
     */
    public function getTotalPaid(int $saleId): float
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM payments 
            WHERE sale_id = ?
        ");
        $stmt->execute([$saleId]);
        return (float)$stmt->fetchColumn();
    }
    
    /**
     * Get remaining balance for a sale
     * 
     * @param int $saleId
     * @return float
     */
    public function getBalance(int $saleId): float
    {
        $totals = $this->getSaleTotals($saleId);
        return $totals['balance'];
    }
    
    /**
     * Check if sale is fully paid
     * 
     * @param int $saleId
     * @return bool
     */
    public function isFullyPaid(int $saleId): bool
    {
        $totals = $this->getSaleTotals($saleId);
        return $totals['is_fully_paid'];
    }
    
    /**
     * Get payment summary for display
     * 
     * @param int $saleId
     * @return array
     */
    public function getPaymentSummary(int $saleId): array
    {
        $totals = $this->getSaleTotals($saleId);
        
        // Get list of payments
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
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'totals' => $totals,
            'payments' => $payments,
            'payment_count' => count($payments)
        ];
    }
}