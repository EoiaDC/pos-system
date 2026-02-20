<?php
namespace Pos\Purchasing;

class SuppliersController
{
    public function index()
    {
        // Load the suppliers view
        require_once __DIR__ . '/../../views/purchasing/suppliers.php';
    }
}