<?php

namespace POS\Sales;

use App\Core\Auth;
use App\Core\Response;
use App\Core\BirReadiness;

class SaleLineController
{
    /**
     * Add a line item to a draft sale
     */
    public function add(): void
    {
        // Check login FIRST
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // THEN check BIR readiness
        BirReadiness::enforceOrRedirect();
        
        // Get and validate inputs
        $saleId = $_POST['sale_id'] ?? '';
        $itemId = $_POST['item_id'] ?? '';
        $qty = $_POST['qty'] ?? 1;
        $discount = $_POST['line_discount'] ?? 0;
        
        if (!is_numeric($saleId) || $saleId <= 0) {
            Response::flash('error', 'Invalid sale ID');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (!is_numeric($itemId) || $itemId <= 0) {
            Response::flash('error', 'Please select a valid item');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (!is_numeric($qty) || $qty <= 0) {
            Response::flash('error', 'Quantity must be greater than 0');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (!is_numeric($discount) || $discount < 0) {
            $discount = 0;
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
            
            // Verify sale exists and is DRAFT
            $stmt = $db->prepare("SELECT id, status FROM sales_headers WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$sale || $sale['status'] !== 'DRAFT') {
                Response::flash('error', 'Sale not found or not in DRAFT status');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Get item details from database
            $stmt = $db->prepare("SELECT item_name, selling_price FROM items WHERE id = ?");
            $stmt->execute([$itemId]);
            $item = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$item) {
                Response::flash('error', 'Item not found');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            $itemName = $item['item_name'];
            $unitPrice = $item['selling_price'];
            
            // Calculate totals with discount validation
            $gross = $qty * $unitPrice;
            
            if ($discount > $gross) {
                Response::flash('error', 'Discount cannot exceed item subtotal (₱' . number_format($gross, 2) . ')');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            $lineTotal = $gross - $discount;
            
            // Insert line with discount
            $stmt = $db->prepare("
                INSERT INTO sales_lines 
                (sale_id, item_id, description, qty, unit_price, line_discount, line_total)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $saleId,
                $itemId,
                $itemName,
                $qty,
                $unitPrice,
                $discount,
                $lineTotal
            ]);
            
            $lineId = $db->lastInsertId();
            
            // Recalculate header totals
            $this->recalculateTotals($db, $saleId);
            
            // Audit log
            $userId = Auth::userId() ?? 0;
            $username = Auth::user()['username'] ?? 'system';
            $metadata = json_encode([
                'sale_id' => $saleId,
                'line_id' => $lineId,
                'item_id' => $itemId,
                'description' => $itemName,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'line_discount' => $discount,
                'line_total' => $lineTotal,
                'gross' => $gross
            ]);
            
            $stmt = $db->prepare("
                INSERT INTO audit_logs 
                (user_id, username, action, table_name, record_id, new_data, ip_address, user_agent)
                VALUES (?, ?, ?, 'sales_lines', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $username,
                'sale.line.added',
                $saleId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $db->commit();
            
            Response::flash('success', 'Item added to sale');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("SaleLineController::add error: " . $e->getMessage());
            Response::flash('error', 'Failed to add item');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
    
    /**
     * Remove a line item from a draft sale
     */
    public function remove(): void
    {
        // Check login
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // THEN check BIR readiness
        BirReadiness::enforceOrRedirect();
        
        // Get inputs
        $lineId = $_POST['line_id'] ?? '';
        $saleId = $_POST['sale_id'] ?? '';
        
        if (!is_numeric($lineId) || $lineId <= 0 || !is_numeric($saleId) || $saleId <= 0) {
            Response::flash('error', 'Invalid request');
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
            
            // Verify sale exists and is DRAFT
            $stmt = $db->prepare("SELECT status FROM sales_headers WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$sale || $sale['status'] !== 'DRAFT') {
                Response::flash('error', 'Sale not found or not in DRAFT status');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Get line details for audit
            $stmt = $db->prepare("SELECT * FROM sales_lines WHERE id = ? AND sale_id = ?");
            $stmt->execute([$lineId, $saleId]);
            $line = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$line) {
                Response::flash('error', 'Line item not found');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Delete the line
            $stmt = $db->prepare("DELETE FROM sales_lines WHERE id = ?");
            $stmt->execute([$lineId]);
            
            // Recalculate header totals
            $this->recalculateTotals($db, $saleId);
            
            // Audit log
            $userId = Auth::userId() ?? 0;
            $username = Auth::user()['username'] ?? 'system';
            $metadata = json_encode([
                'sale_id' => $saleId,
                'line_id' => $lineId,
                'description' => $line['description'],
                'qty' => $line['qty'],
                'price' => $line['unit_price']
            ]);
            
            $stmt = $db->prepare("
                INSERT INTO audit_logs 
                (user_id, username, action, table_name, record_id, new_data, ip_address, user_agent)
                VALUES (?, ?, ?, 'sales_lines', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $username,
                'sale.line.removed',
                $saleId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $db->commit();
            
            Response::flash('success', 'Item removed from sale');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("SaleLineController::remove error: " . $e->getMessage());
            Response::flash('error', 'Failed to remove item');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
    
    /**
     * Recalculate header totals based on lines
     */
    private function recalculateTotals(\PDO $db, int $saleId): void
    {
        // Sum all line totals
        $stmt = $db->prepare("
            SELECT 
                COALESCE(SUM(line_total), 0) as subtotal,
                COUNT(*) as line_count
            FROM sales_lines 
            WHERE sale_id = ?
        ");
        $stmt->execute([$saleId]);
        $totals = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $subtotal = $totals['subtotal'];
        $grandTotal = $subtotal; // No tax/discount yet
        
        // Update header
        $stmt = $db->prepare("
            UPDATE sales_headers 
            SET subtotal = ?, grand_total = ?
            WHERE id = ?
        ");
        $stmt->execute([$subtotal, $grandTotal, $saleId]);
    }
}