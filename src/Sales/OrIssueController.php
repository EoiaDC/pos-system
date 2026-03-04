<?php

namespace POS\Sales;

use App\Core\Auth;
use App\Core\Response;
use App\Core\OrIssuanceService;

class OrIssueController
{
    /**
     * Issue an OR number for a posted sale
     */
    public function issue(): void
    {
        // Check login
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
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
        
        // Load sale header to check preconditions
        $stmt = $db->prepare("
            SELECT * FROM sales_headers 
            WHERE id = ?
        ");
        $stmt->execute([$saleId]);
        $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$sale) {
            Response::flash('error', 'Sale not found');
            Response::redirect('/sales');
            return;
        }
        
        // Preconditions
        if ($sale['status'] !== 'POSTED') {
            Response::flash('error', 'Sale must be POSTED before issuing OR');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (empty($sale['or_series_id'])) {
            Response::flash('error', 'No OR series selected for this sale');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        if (!empty($sale['or_no'])) {
            Response::flash('error', 'OR already issued for this sale');
            Response::redirect('/sales/draft?sale_id=' . $saleId);
            return;
        }
        
        // Call Core service to issue OR atomically
        $userId = Auth::userId() ?? 0;
        $issuedOrNo = OrIssuanceService::issueForSale($saleId, $userId);
        
        if ($issuedOrNo > 0) {
            Response::flash('success', 'OR issued: ' . $issuedOrNo);
        } else {
            Response::flash('error', 'Failed to issue OR. Series may be exhausted or inactive.');
        }
        
        Response::redirect('/sales/draft?sale_id=' . $saleId);
    }
}