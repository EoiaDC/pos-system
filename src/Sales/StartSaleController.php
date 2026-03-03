<?php

namespace POS\Sales;

use POS\Core\Auth;
use POS\Core\Response;
use POS\Core\BirReadiness;

class StartSaleController
{
    /**
     * Create a new draft sale header
     * 
     * This is a minimal endpoint that:
     * 1. Checks BIR readiness
     * 2. Creates draft sale record
     * 3. Logs audit event
     * 4. Returns success (will redirect to cart later)
     */
    public function index(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Check BIR readiness (reuse the gate from Step 7)
        if (!BirReadiness::isReadyForSales()) {
            Response::flash('error', 'Cannot start sale: BIR requirements not met');
            Response::redirect('/sales/bir-readiness');
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
            // Generate unique transaction number
            $transactionNumber = 'SALE-' . date('Ymd') . '-' . uniqid();
            
            // Insert draft sale header
            $sql = "INSERT INTO sales_headers (
                transaction_number, register_id, created_by, status
            ) VALUES (?, ?, ?, 'draft')";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $transactionNumber,
                null, // register_id will be set later
                Auth::userId()
            ]);
            
            $salesHeaderId = $db->lastInsertId();
            
            // Log audit event
            SalesAudit::logSaleStarted($salesHeaderId, [
                'transaction_number' => $transactionNumber,
                'user_id' => Auth::userId()
            ]);
            
            // Success! Redirect to cart page (placeholder for now)
            Response::flash('success', 'Draft sale created: ' . $transactionNumber);
            Response::redirect('/sales/cart?id=' . $salesHeaderId);
            
        } catch (\Exception $e) {
            // Log error
            error_log("StartSaleController error: " . $e->getMessage());
            
            Response::flash('error', 'Failed to start sale. Please try again.');
            Response::redirect('/sales');
        }
    }
}