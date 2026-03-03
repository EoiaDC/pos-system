<?php

namespace POS\Sales;

use POS\Core\Auth;
use POS\Core\Response;
use POS\Core\Validator;

class SaleOrSeriesController
{
    /**
     * Update the OR series for a draft sale
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
            // Begin transaction
            $db->beginTransaction();
            
            // Verify the OR series exists and is active
            $stmt = $db->prepare("SELECT id FROM or_series WHERE id = ? AND is_active = 1");
            $stmt->execute([$seriesId]);
            if (!$stmt->fetch()) {
                Response::flash('error', 'Selected OR series is not active or does not exist');
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Update the sale header
            $stmt = $db->prepare("UPDATE sales_headers SET or_series_id = ? WHERE id = ? AND status = 'DRAFT'");
            $stmt->execute([$seriesId, $saleId]);
            
            if ($stmt->rowCount() === 0) {
                Response::flash('error', 'Sale not found or not in DRAFT status');
                $db->rollBack();
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Log audit event (will be handled by DEV D's service)
            
            $db->commit();
            
            Response::flash('success', 'OR series selected successfully');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("SaleOrSeriesController error: " . $e->getMessage());
            Response::flash('error', 'Database error occurred');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
        }
    }
}