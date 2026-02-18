<?php

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/Connection.php';
require __DIR__ . '/DB.php';

$row = DB::fetch("SELECT 1 AS ok");

if ($row && (int)$row['ok'] === 1) {
    echo "DB OK";
} else {
    echo "DB FAIL";
}
