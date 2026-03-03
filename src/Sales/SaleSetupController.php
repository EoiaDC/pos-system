<?php

namespace POS\Sales;

use POS\Core\Auth;
use POS\Core\Response;
use POS\Core\BirReadiness;

class SaleSetupController
{
    /**
     * Set register for a draft sale
     */
    public function setRegister(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Get and validate inputs
        $saleId = $_POST['sale_id'] ?? '';
        $registerId = $_POST['register_id'] ?? '';
        
        if (!is_numeric($saleId) || $saleId <= 0) {
            Response::flash('error', 'Invalid sale ID');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (!is_numeric($registerId) || $registerId <= 0) {
            Response::flash('error', 'Please select a valid register');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
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
            
            // Verify the sale exists and is DRAFT (LOCKING RULE)
            $stmt = $db->prepare("SELECT id, status FROM sales_headers WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$sale) {
                Response::flash('error', 'Sale not found');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            if ($sale['status'] !== 'DRAFT') {
                Response::flash('error', 'Cannot modify sale - status is ' . $sale['status']);
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Verify the register exists and is active
            $stmt = $db->prepare("SELECT id, register_code FROM pos_registers WHERE id = ? AND is_active = 1");
            $stmt->execute([$registerId]);
            $register = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$register) {
                Response::flash('error', 'Selected register is not active or does not exist');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Update the sale header
            $stmt = $db->prepare("UPDATE sales_headers SET pos_register_id = ? WHERE id = ?");
            $stmt->execute([$registerId, $saleId]);
            
            // AUDIT: Log the register selection
            $userId = Auth::userId() ?? 0;
            $username = Auth::user()['username'] ?? 'system';
            $metadata = json_encode([
                'sale_id' => $saleId,
                'pos_register_id' => $registerId,
                'register_code' => $register['register_code'],
                'previous_register_id' => $sale['pos_register_id'] ?? null
            ]);
            
            $stmt = $db->prepare("
                INSERT INTO audit_logs 
                (user_id, username, action, table_name, record_id, new_data, ip_address, user_agent)
                VALUES (?, ?, ?, 'sales_headers', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $username,
                'sale.register.selected',
                $saleId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $db->commit();
            
            Response::flash('success', 'Register selected successfully');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("SaleSetupController::setRegister error: " . $e->getMessage());
            Response::flash('error', 'Database error occurred');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
    
    /**
     * Set OR series for a draft sale
     */
    public function setOrSeries(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Get and validate inputs
        $saleId = $_POST['sale_id'] ?? '';
        $seriesId = $_POST['or_series_id'] ?? '';
        
        if (!is_numeric($saleId) || $saleId <= 0) {
            Response::flash('error', 'Invalid sale ID');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (!is_numeric($seriesId) || $seriesId <= 0) {
            Response::flash('error', 'Please select a valid OR series');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
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
            
            // Verify the sale exists and is DRAFT (LOCKING RULE)
            $stmt = $db->prepare("SELECT id, status FROM sales_headers WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$sale) {
                Response::flash('error', 'Sale not found');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            if ($sale['status'] !== 'DRAFT') {
                Response::flash('error', 'Cannot modify sale - status is ' . $sale['status']);
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Verify the OR series exists, is active, and is usable
            $stmt = $db->prepare("
                SELECT id, series_code FROM or_series 
                WHERE id = ? 
                  AND is_active = 1 
                  AND current_no BETWEEN start_no AND end_no
            ");
            $stmt->execute([$seriesId]);
            $series = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$series) {
                Response::flash('error', 'Selected OR series is not active, not usable, or does not exist');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Update the sale header
            $stmt = $db->prepare("UPDATE sales_headers SET or_series_id = ? WHERE id = ?");
            $stmt->execute([$seriesId, $saleId]);
            
            // AUDIT: Log the OR series selection
            $userId = Auth::userId() ?? 0;
            $username = Auth::user()['username'] ?? 'system';
            $metadata = json_encode([
                'sale_id' => $saleId,
                'or_series_id' => $seriesId,
                'series_code' => $series['series_code'],
                'previous_series_id' => $sale['or_series_id'] ?? null
            ]);
            
            $stmt = $db->prepare("
                INSERT INTO audit_logs 
                (user_id, username, action, table_name, record_id, new_data, ip_address, user_agent)
                VALUES (?, ?, ?, 'sales_headers', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $username,
                'sale.or_series.selected',
                $saleId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $db->commit();
            
            Response::flash('success', 'OR series selected successfully');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("SaleSetupController::setOrSeries error: " . $e->getMessage());
            Response::flash('error', 'Database error occurred');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
}