<?php

namespace POS\Sales;

class SalesHistoryController
{
    /**
     * Display sales history placeholder page
     */
    public function index(): void
    {
        include __DIR__ . '/../../views/sales/history.php';
    }
}