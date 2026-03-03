<?php

namespace POS\Sales;

class NewSaleController
{
    /**
     * Display new sale placeholder page
     */
    public function index(): void
    {
        include __DIR__ . '/../../views/sales/new_sale.php';
    }
}