<?php

namespace POS\Sales;

use POS\Core\Auth;
use POS\Core\Response;
use POS\Core\Validator;

class SaleRegisterController
{
    /**
     * Update the register for a draft sale
     */
    public function update(): void
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
            // Begin transaction
            $db->beginTransaction();
            
            // Verify the register exists and is active
            $stmt = $db->prepare("SELECT id FROM pos_registers WHERE id = ? AND is_active = 1");
            $stmt->execute([$registerId]);
            if (!$stmt->fetch()) {
                Response::flash('error', 'Selected register is not active or does not exist');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Update the sale header
            $stmt = $db->prepare("UPDATE sales_headers SET pos_register_id = ? WHERE id = ? AND status = 'DRAFT'");
            $stmt->execute([$registerId, $saleId]);
            
            if ($stmt->rowCount() === 0) {
                Response::flash('error', 'Sale not found or not in DRAFT status');
                $db->rollBack();
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Log audit event (will be handled by DEV D's service)
            // For now, we can note it in a comment or call a helper if available
            
            $db->commit();
            
            Response::flash('success', 'Register selected successfully');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("SaleRegisterController error: " . $e->getMessage());
            Response::flash('error', 'Database error occurred');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
}