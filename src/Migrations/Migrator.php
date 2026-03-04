<?php

require_once __DIR__ . '/../Database/DB.php';
require_once __DIR__ . '/MigrationLoader.php';

use Pos\Database\DB;

class Migrator
{
    private string $migrationsPath;

    public function __construct(string $migrationsPath)
    {
        $this->migrationsPath = $migrationsPath;
    }

    public function ensureMigrationsTable(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS schema_migrations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL UNIQUE,
                batch INT NOT NULL,
                applied_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    private function nextBatch(PDO $pdo): int
    {
        $stmt = $pdo->query("SELECT MAX(batch) AS max_batch FROM schema_migrations");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $max = $row && $row['max_batch'] !== null ? (int)$row['max_batch'] : 0;
        return $max + 1;
    }

    private function appliedFilenames(PDO $pdo): array
    {
        $stmt = $pdo->query("SELECT filename FROM schema_migrations");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $set = [];
        foreach ($rows as $r) $set[$r['filename']] = true;
        return $set;
    }

    public function up(): array
    {
        echo "=== Starting Migration Process ===\n";
        
        $schemaPdo = DB::connect();
        $schemaPdo->exec("DROP TABLE IF EXISTS schema_migrations");
        $this->ensureMigrationsTable($schemaPdo);
        echo "✓ Migrations table recreated\n";

        $loader = new MigrationLoader();
        $files = $loader->listMigrationFiles($this->migrationsPath);

        $applied = $this->appliedFilenames($schemaPdo);
        $batch = $this->nextBatch($schemaPdo);

        $appliedNow = [];

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            
            if (isset($applied[$filename])) {
                echo "⏩ Skipping $filename (already applied)\n";
                continue;
            }

            echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "Processing: $filename\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            
            $migration = $loader->load($filePath);
            $migratePdo = DB::connect();
            
            echo "✓ Database connection established\n";
            
            // Check if this is a CREATE TABLE migration by filename
            $isCreateTable = strpos($filename, 'create_') !== false;
            
            if ($isCreateTable) {
                try {
                    $migration['up']($migratePdo);
                    echo "✓ Migration up() completed\n";
                    
                    $recordStmt = $schemaPdo->prepare(
                        "INSERT INTO schema_migrations (filename, batch, applied_at) VALUES (?, ?, ?)"
                    );
                    $recordStmt->execute([$filename, $batch, date('Y-m-d H:i:s')]);
                    echo "✓ Migration recorded\n";
                    
                    $appliedNow[] = $filename;
                    echo "✅ SUCCESS: $filename applied\n";
                    
                } catch (Exception $e) {
                    echo "❌ ERROR: " . $e->getMessage() . "\n";
                    throw $e;
                }
            } else {
                $migratePdo->beginTransaction();
                
                try {
                    $migration['up']($migratePdo);
                    echo "✓ Migration up() completed\n";
                    
                    $migratePdo->commit();
                    echo "✓ Transaction committed\n";
                    
                    $recordStmt = $schemaPdo->prepare(
                        "INSERT INTO schema_migrations (filename, batch, applied_at) VALUES (?, ?, ?)"
                    );
                    $recordStmt->execute([$filename, $batch, date('Y-m-d H:i:s')]);
                    echo "✓ Migration recorded\n";
                    
                    $appliedNow[] = $filename;
                    echo "✅ SUCCESS: $filename applied\n";
                    
                } catch (Exception $e) {
                    if ($migratePdo->inTransaction()) {
                        $migratePdo->rollBack();
                    }
                    throw $e;
                }
            }
            
            $migratePdo = null;
        }

        echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "=== Migration Process Complete ===\n";
        echo "Applied " . count($appliedNow) . " migrations\n";

        return $appliedNow;
    }
}