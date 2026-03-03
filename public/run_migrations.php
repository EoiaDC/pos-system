<?php
require_once __DIR__ . '/config/bootstrap.php';

$migrations = glob(__DIR__ . '/migrations/*.php');
sort($migrations); // ensure order
foreach ($migrations as $file) {
    echo "Running $file...\n";
    require $file;
    echo "Done.\n";
}