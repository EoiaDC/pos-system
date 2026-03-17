<?php
// Load bootstrap – this loads env, sets up autoloader, etc.
require_once __DIR__ . '/config/bootstrap.php';

// Now load database classes (bootstrap might already have them, but we'll be explicit)
require_once __DIR__ . '/src/Database/Connection.php';
require_once __DIR__ . '/src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

$config = require __DIR__ . '/config/database.php';
Connection::loadConfig($config);

$count = DB::fetch('SELECT COUNT(*) as cnt FROM items');
echo 'Items in database: ' . $count['cnt'] . "\n";
