<?php
// Hardcoded database config (same as your working PDO test)
$host = 'localhost';
$port = '3306';
$dbname = 'pos_integration';
$user = 'pos_user';
$pass = 'secure_password_here';  // <-- Make sure this is your actual password
$charset = 'utf8mb4';

// Manually establish PDO connection
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Get all migration files
$migrationFiles = glob(__DIR__ . '/migrations/*.php');
sort($migrationFiles);

echo "🚀 Running migrations...\n\n";
$count = 0;

foreach ($migrationFiles as $file) {
    echo "▶️  " . basename($file) . "... ";
    try {
        // Load the migration file – it can use the $pdo object if needed
        require $file;
        echo "✅\n";
        $count++;
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Completed: $count migrations executed.\n";
