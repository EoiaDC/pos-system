<?php
/**
 * Sale Finalization Service
 * 
 * Handles finalizing fully paid sales
 * 
 * @package POS\Services
 */

namespace POS\Services;

use PDO;
use POS\Services\SalePaymentTotalsService;

class SaleFinalizationService
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
     * Finalize a fully paid sale
     * 
     * @param int $saleId
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function finalizeSale(int $saleId, int $userId): array
    {
        $this->db->beginTransaction();
        
        try {
            // Get sale and validate it can be finalized
            $sale = $this->validateSaleForFinalization($saleId);
            
            // Check if fully paid
            if (!$this->totalsService->isFullyPaid($saleId)) {
                $totals = $this->totalsService->getSaleTotals($saleId);
                throw new \Exception(
                    'Cannot finalize: Sale is not fully paid. ' .
                    'Balance: ₱' . number_format($totals['balance'], 2)
                );
            }
            
            // Update sale status to FINALIZED
            $stmt = $this->db->prepare("
                UPDATE sales_headers 
                SET status = 'FINALIZED',
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$saleId]);
            
            // Check if finalized_at column exists and update it
            $this->updateFinalizedTimestamp($saleId);
            
            // Create audit log
            $this->createAuditLog($saleId, $userId, $sale);
            
            $this->db->commit();
            
            return [
                'sale_id' => $saleId,
                'status' => 'FINALIZED',
                'message' => 'Sale finalized successfully'
            ];
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Validate sale exists and is in correct state for finalization
     * 
     * @param int $saleId
     * @return array
     * @throws \Exception
     */
    private function validateSaleForFinalization(int $saleId): array
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
        if (strtoupper($sale['status']) === 'FINALIZED') {
            throw new \Exception('Sale is already finalized');
        }
        
        // Must be POSTED (not DRAFT)
        if (strtoupper($sale['status']) !== 'POSTED') {
            throw new \Exception('Only POSTED sales can be finalized (current status: ' . $sale['status'] . ')');
        }
        
        return $sale;
    }
    
    /**
     * Update finalized_at timestamp if column exists
     * 
     * @param int $saleId
     */
    private function updateFinalizedTimestamp(int $saleId): void
    {
        try {
            // Check if finalized_at column exists
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as col_exists
                FROM information_schema.columns 
                WHERE table_name = 'sales_headers' 
                AND column_name = 'finalized_at'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['col_exists'] > 0) {
                $updateStmt = $this->db->prepare("
                    UPDATE sales_headers 
                    SET finalized_at = NOW() 
                    WHERE id = ?
                ");
                $updateStmt->execute([$saleId]);
            }
        } catch (\Exception $e) {
            // Column doesn't exist or other error - silently continue
            // This is acceptable per Week 1 requirements
            error_log('Finalized timestamp update skipped: ' . $e->getMessage());
        }
    }
    
    /**
     * Create audit log for finalization
     * 
     * @param int $saleId
     * @param int $userId
     * @param array $sale
     */
    private function createAuditLog(int $saleId, int $userId, array $sale): void
    {
        try {
            // Get username
            $stmt = $this->db->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $user['username'] ?? 'system';
            
            // Get payment totals for context
            $totals = $this->totalsService->getSaleTotals($saleId);
            
            $metadata = json_encode([
                'sale_id' => $saleId,
                'previous_status' => $sale['status'],
                'new_status' => 'FINALIZED',
                'total_amount' => $totals['total_amount'],
                'total_paid' => $totals['total_paid'],
                'finalized_at' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $this->db->prepare("
                INSERT INTO audit_logs 
                (user_id, username, action, table_name, record_id, new_data, ip_address, user_agent, created_at)
                VALUES (?, ?, 'sale.finalized', 'sales_headers', ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $username,
                $saleId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ]);
            
        } catch (\Exception $e) {
            // If audit_logs table doesn't exist, just log to error log
            error_log('Audit log failed for sale finalization: ' . $e->getMessage());
            error_log('Sale finalized: ' . json_encode([
                'sale_id' => $saleId,
                'user_id' => $userId,
                'totals' => $totals ?? null
            ]));
        }
    }
    
    /**
     * Check if a sale can be finalized
     * 
     * @param int $saleId
     * @return array
     */
    public function canFinalize(int $saleId): array
    {
        try {
            // Check if sale exists
            $stmt = $this->db->prepare("SELECT id, status FROM sales_headers WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$sale) {
                return [
                    'can_finalize' => false,
                    'reason' => 'Sale not found'
                ];
            }
            
            // Check status
            if (strtoupper($sale['status']) !== 'POSTED') {
                return [
                    'can_finalize' => false,
                    'reason' => 'Sale must be POSTED (current: ' . $sale['status'] . ')'
                ];
            }
            
            // Check if fully paid
            if (!$this->totalsService->isFullyPaid($saleId)) {
                $totals = $this->totalsService->getSaleTotals($saleId);
                return [
                    'can_finalize' => false,
                    'reason' => 'Sale not fully paid. Balance: ₱' . number_format($totals['balance'], 2)
                ];
            }
            
            return [
                'can_finalize' => true,
                'reason' => 'Ready to finalize'
            ];
            
        } catch (\Exception $e) {
            return [
                'can_finalize' => false,
                'reason' => 'Error checking finalization: ' . $e->getMessage()
            ];
        }
    }
}