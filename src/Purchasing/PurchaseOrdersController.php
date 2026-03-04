<?php
namespace Pos\Purchasing;

class PurchaseOrdersController
{
    public function index()
    {
        // Load the purchase orders view
        require_once __DIR__ . '/../../views/purchasing/purchase_orders.php';
    }
}