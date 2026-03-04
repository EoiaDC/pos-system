<?php

namespace POS\Sales;

use App\Core\Auth;
use App\Core\Response;
use App\Core\Validator;
use App\Core\BirReadiness;

class SaleDraftController
{
    /**
     * Display draft sale details loaded from database
     */
    public function index(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Get and validate sale_id from query string
        $saleId = $_GET['sale_id'] ?? '';
        
        // Simple validation
        if (!is_numeric($saleId) || $saleId <= 0) {
            // Pass error to view instead of redirect
            $error = 'Invalid sale ID provided';
            include __DIR__ . '/../../views/sales/draft.php';
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
        
        // Load sale header
        $stmt = $db->prepare("
            SELECT sh.*, 
                   u.username as created_by_username
            FROM sales_headers sh
            LEFT JOIN users u ON sh.created_by = u.id
            WHERE sh.id = ?
        ");
        $stmt->execute([$saleId]);
        $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Load active registers for dropdown
        $stmt = $db->query("
            SELECT id, register_code, register_name 
            FROM pos_registers 
            WHERE is_active = 1 
            ORDER BY register_code
        ");
        $activeRegisters = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Load active and usable OR series for dropdown
        $stmt = $db->query("
            SELECT id, series_code, start_no, end_no, current_no 
            FROM or_series 
            WHERE is_active = 1 
              AND current_no BETWEEN start_no AND end_no
            ORDER BY series_code
        ");
        $activeOrSeries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Load active items for dropdown
        $stmt = $db->query("
            SELECT id, item_code, item_name, selling_price 
            FROM items 
            WHERE is_active = 1 OR is_active IS NULL
            ORDER BY item_name
            LIMIT 100
        ");
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Load lines for this sale and count them
        $stmt = $db->prepare("
            SELECT * FROM sales_lines 
            WHERE sale_id = ?
            ORDER BY id ASC
        ");
        $stmt->execute([$saleId]);
        $lines = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Set line count for preconditions display
        $lineCount = count($lines);
        
        // Get BIR readiness status for context (read-only)
        $birStatus = BirReadiness::getReadinessStatus();
        
        // Pass data to view
        include __DIR__ . '/../../views/sales/draft.php';
    }
}