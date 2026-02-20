<?php

class InventoryHomeController
{
    public function index()
    {
        // Load the inventory index view
        require __DIR__ . '/../../views/inventory/index.php';
    }
}
