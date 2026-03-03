<?php

namespace POS\Sales;

use POS\Core\Auth;
use POS\Core\Response;
use POS\Core\BirReadiness;

class SaleStartController
{
    /**
     * Display the start sale confirmation page
     */
    public function index(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Check BIR readiness (will redirect if not ready)
        BirReadiness::enforceOrRedirect();
        
        // Display the start sale page
        include __DIR__ . '/../../views/sales/start_sale.php';
    }
    
    /**
     * Create a new draft sale
     */
    public function create(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Check BIR readiness (will redirect if not ready)
        BirReadiness::enforceOrRedirect();
        
        // Get database connection
        $config = require __DIR__ . '/../../config/database.php';
        $db = new \PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
            $config['user'],
            $config['pass']
        );
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        try {
            // Insert draft sale header
            // Note: Using the exact column names from DEV D's migration
            $sql = "INSERT INTO sales_headers (
                status, created_by, created_at
            ) VALUES (?, ?, NOW())";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'DRAFT',
                Auth::userId()
            ]);
            
            $saleId = $db->lastInsertId();
            
            // Log audit event (DEV D will handle this in service later)
            // For now, we can log directly or rely on DEV D's service
            
            // Success! Redirect to draft page
            Response::flash('success', 'Draft sale created successfully');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            
        } catch (\Exception $e) {
            error_log("SaleStartController::create error: " . $e->getMessage());
            Response::flash('error', 'Failed to create draft sale. Please try again.');
            Response::redirect('/sales/start');
        }
    }
}