<?php

require_once __DIR__ . '/../../src/Database/Connection.php';
require_once __DIR__ . '/../../src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

// Load database config
$config = require __DIR__ . '/../../config/database.php';
Connection::loadConfig($config);

class SampleDataController
{
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->insertSampleItem();
        } else {
            $this->showForm();
        }
    }

    private function showForm($message = null, $error = null)
    {
        $category = DB::fetch("SELECT id, name FROM categories LIMIT 1");
        $uom = DB::fetch("SELECT id, code FROM units_of_measure LIMIT 1");
        require __DIR__ . '/../../views/inventory/sample_data.php';
    }

    private function insertSampleItem()
    {
        try {
            // Get first available category and UOM
            $category = DB::fetch("SELECT id FROM categories LIMIT 1");
            $uom = DB::fetch("SELECT id FROM units_of_measure LIMIT 1");

            if (!$category || !$uom) {
                throw new Exception("Please create at least one category and unit of measure first.");
            }

            $sku = 'SAMPLE-' . date('Ymd') . '-' . rand(100, 999);
            $now = date('Y-m-d H:i:s');

            // 9 placeholders for 9 columns
            $sql = "INSERT INTO items (
                sku, name, category_id, uom_id, cost, price,
                taxable, is_active, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            DB::execute($sql, [
                $sku,
                'Sample Item ' . date('Y-m-d H:i:s'),
                (int)$category['id'],
                (int)$uom['id'],
                10.00,  // cost
                19.99,  // price
                1,      // taxable
                1,      // is_active
                $now
            ]);

            $itemId = DB::lastInsertId();
            $message = "✅ Sample item created successfully! Item ID: $itemId, SKU: $sku";
            $this->showForm($message, null);
        } catch (Exception $e) {
            $error = "❌ Error: " . $e->getMessage();
            $this->showForm(null, $error);
        }
    }
}

// Route the request
$controller = new SampleDataController();
$controller->handleRequest();
