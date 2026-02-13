<?php
require 'src/config/bootstrap.php';

echo "✅ Bootstrap loaded successfully!\n";

use Pos\Database\DB;
$db = DB::connect();
echo "✅ Database connected!\n";

use Pos\Migrations\Migrator;
$migrator = new Migrator();
echo "✅ Migrator created!\n";

$pending = $migrator->getPendingMigrations();
echo "Pending migrations: " . count($pending) . "\n";
print_r($pending);

echo "\nRunning migrations...\n";
echo "========================\n";
$result = $migrator->up();
print_r($result);