<?php

namespace POS\Sales;

class BirReadinessController
{
    /**
     * Display BIR readiness checklist
     */
    public function index(): void
    {
        include __DIR__ . '/../../views/sales/bir_readiness.php';
    }
}