<?php

namespace POS\Sales;

use App\Core\Auth;
use App\Core\Response;
use App\Core\BirReadiness;

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
            // Generate a unique transaction number
            $transactionNumber = 'SALE-' . date('Ymd') . '-' . uniqid();
            
            // Insert draft sale header with transaction number
            $sql = "INSERT INTO sales_headers (
                transaction_number, status, created_by, created_at
            ) VALUES (?, 'DRAFT', ?, NOW())";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$transactionNumber, Auth::userId()]);
            
            $saleId = $db->lastInsertId();
            
            Response::flash('success', 'Draft sale created successfully');
            header('Location: /pos-system/public/sales/draft?sale_id=' . $saleId);
            exit;
            
        } catch (\Exception $e) {
            error_log("SaleStartController::create error: " . $e->getMessage());
            echo "<h1>Database Error</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
            exit;
        }
    }
}