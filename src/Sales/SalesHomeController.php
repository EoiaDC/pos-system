<?php

namespace POS\Sales;

class SalesHomeController
{
    /**
     * Display sales module home page
     */
    public function index(): void
    {
        // Simply include the view
        include __DIR__ . '/../../views/sales/index.php';
    }
}