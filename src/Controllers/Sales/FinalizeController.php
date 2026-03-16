<?php
/**
 * Finalize Controller
 * 
 * Handles finalizing fully paid sales
 * 
 * @package POS\Controllers\Sales
 */

namespace POS\Controllers\Sales;

use App\Core\Auth;
use App\Core\Response;
use POS\Services\SaleFinalizationService;
use POS\Services\SalePaymentTotalsService;

class FinalizeController
{
    /**
     * Finalize a fully paid sale
     */
    public function finalize(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Response::redirect('/login');
            return;
        }
        
        // Get sale ID from POST
        $saleId = (int)($_POST['sale_id'] ?? 0);
        
        if ($saleId <= 0) {
            Response::flash('error', 'Invalid sale ID');
            Response::redirect('/sales');
            return;
        }
        
        try {
            // Check if sale can be finalized
            $finalizeService = new SaleFinalizationService();
            $totalsService = new SalePaymentTotalsService();
            
            $canFinalize = $finalizeService->canFinalize($saleId);
            
            if (!$canFinalize['can_finalize']) {
                Response::flash('error', 'Cannot finalize: ' . $canFinalize['reason']);
                Response::redirect('/sales/draft?sale_id=' . $saleId);
                return;
            }
            
            // Perform finalization
            $userId = Auth::userId() ?? 0;
            $result = $finalizeService->finalizeSale($saleId, $userId);
            
            // Success message
            Response::flash('success', '✅ Sale #' . $saleId . ' finalized successfully!');
            
        } catch (\Exception $e) {
            // Log error
            error_log('Finalization error: ' . $e->getMessage());
            
            // User-friendly error
            Response::flash('error', 'Failed to finalize sale: ' . $e->getMessage());
        }
        
        // Redirect back to sale page
        Response::redirect('/sales/draft?sale_id=' . $saleId);
    }
}