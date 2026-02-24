<?php

require_once 'src/Database/Connection.php';
require_once 'src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

$config = require 'config/database.php';
Connection::loadConfig($config);

echo "Inserting test data...\n";

// Insert category
DB::execute(
    "INSERT INTO categories (name, created_at) VALUES (?, ?)",
    ['Test Category', date('Y-m-d H:i:s')]
);
echo "✅ Category created\n";

// Insert UOM
DB::execute(
    "INSERT INTO units_of_measure (code, name, created_at) VALUES (?, ?, ?)",
    ['PC', 'Piece', date('Y-m-d H:i:s')]
);
echo "✅ Unit of Measure created\n";

// Verify
$category = DB::fetch("SELECT id, name FROM categories LIMIT 1");
$uom = DB::fetch("SELECT id, code FROM units_of_measure LIMIT 1");

echo "\nVerification:\n";
echo "Category: " . ($category ? $category['name'] . " (ID: " . $category['id'] . ")" : "None") . "\n";
echo "UOM: " . ($uom ? $uom['code'] . " (ID: " . $uom['id'] . ")" : "None") . "\n";
