<?php

namespace POS\Sales;

use POS\Core\Auth;
use POS\Core\Response;
use POS\Core\BirReadiness;

class SalePostController
{
    /**
     * Post a draft sale (mark as POSTED, lock editing)
     */
    public function post(): void
    {
        // Check login
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Enforce BIR readiness
        BirReadiness::enforceOrRedirect();
        
        // Get sale_id
        $saleId = $_POST['sale_id'] ?? '';
        
        if (!is_numeric($saleId) || $saleId <= 0) {
            Response::flash('error', 'Invalid sale ID');
            Response::redirect('/sales');
            return;
        }
        
        // Get database connection
        $config = require __DIR__ . '/../../config/database.php';
        $db = new \PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
            $config['user'],
            $config['pass']
        );
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        try {
            $db->beginTransaction();
            
            // Load sale header with all needed fields
            $stmt = $db->prepare("
                SELECT sh.*, 
                       COUNT(l.id) as line_count
                FROM sales_headers sh
                LEFT JOIN sales_lines l ON sh.id = l.sale_id
                WHERE sh.id = ?
                GROUP BY sh.id
            ");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$sale) {
                Response::flash('error', 'Sale not found');
                Response::redirect('/sales');
                return;
            }
            
            // Check all preconditions
            
            // 1. Status must be DRAFT
            if ($sale['status'] !== 'DRAFT') {
                Response::flash('error', 'Sale is already ' . $sale['status'] . ' and cannot be posted');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // 2. Must have at least one line
            if ($sale['line_count'] == 0) {
                Response::flash('error', 'Cannot post sale with no items');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // 3. Register must be selected
            if (empty($sale['pos_register_id'])) {
                Response::flash('error', 'Please select a POS register before posting');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // 4. OR series must be selected
            if (empty($sale['or_series_id'])) {
                Response::flash('error', 'Please select an OR series before posting');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // 5. Grand total must be >= 0
            if ($sale['grand_total'] < 0) {
                Response::flash('error', 'Invalid grand total (negative)');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // All checks passed — update status to POSTED
            $stmt = $db->prepare("
                UPDATE sales_headers 
                SET status = 'POSTED', updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$saleId]);
            
            // Audit log
            $userId = Auth::userId() ?? 0;
            $username = Auth::user()['username'] ?? 'system';
            $metadata = json_encode([
                'sale_id' => $saleId,
                'subtotal' => $sale['subtotal'],
                'discount_total' => $sale['discount_total'] ?? 0,
                'grand_total' => $sale['grand_total'],
                'line_count' => $sale['line_count'],
                'pos_register_id' => $sale['pos_register_id'],
                'or_series_id' => $sale['or_series_id'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $db->prepare("
                INSERT INTO audit_logs 
                (user_id, username, action, table_name, record_id, new_data, ip_address, user_agent)
                VALUES (?, ?, ?, 'sales_headers', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $username,
                'sale.posted',
                $saleId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $db->commit();
            
            Response::flash('success', 'Sale posted successfully');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("SalePostController::post error: " . $e->getMessage());
            Response::flash('error', 'Failed to post sale');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
}