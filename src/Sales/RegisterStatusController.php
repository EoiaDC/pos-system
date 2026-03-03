<?php

namespace POS\Sales;

class RegisterStatusController
{
    /**
     * Display register status placeholder page
     * Shows what register and OR series would be active
     */
    public function index(): void
    {
        include __DIR__ . '/../../views/sales/register_status.php';
    }
}