<?php
namespace Pos\Purchasing;

class ReceivingController
{
    public function index()
    {
        // Load the receiving view
        require_once __DIR__ . '/../../views/purchasing/receiving.php';
    }
}