<?php
namespace Pos\Purchasing;

class PurchasingHomeController
{
    public function index()
    {
        // Load the purchasing index view
        require_once __DIR__ . '/../../views/purchasing/index.php';
    }
}