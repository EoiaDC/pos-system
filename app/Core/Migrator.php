<?php
namespace App\Core;

class Migrator
{
    private \PDO $pdo;
    private string $migrationPath;
    private int $batch;
    private array $output = [];
    
    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
        $this->migrationPath = __DIR__ . '/../../db/migrations/';
        $this->ensureMigrationTableExists();
        $this->batch = $this->getNextBatchNumber();
    }
    
    private function ensureMigrationTableExists(): void
    {
        // Check if migrations table exists
        $result = $this->pdo->query("
            SELECT COUNT(*) as count 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() 
            AND table_name = 'migrations'
        ");
        
        if ($result->fetch()['count'] == 0) {
            // Create migrations table from the init file
            $initSql = file_get_contents($this->migrationPath . '000_init_migrations.sql');
            $this->pdo->exec($initSql);
            $this->output[] = "✓ Created migrations tracking table";
        }
    }
    
    private function getNextBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT COALESCE(MAX(batch), 0) + 1 as next_batch FROM migrations");
        return (int) $stmt->fetch()['next_batch'];
    }
    
    public function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations ORDER BY migration");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    public function getPendingMigrations(): array
    {
        $applied = $this->getAppliedMigrations();
        $all = $this->getMigrationFiles();
        
        return array_values(array_diff($all, $applied));
    }
    
    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationPath . '*.sql');
        $files = array_map(function($file) {
            return basename($file);
        }, $files);
        
        sort($files);
        return $files;
    }
    
    public function showStatus(bool $dryRun = false): void
    {
        $applied = $this->getAppliedMigrations();
        $pending = $this->getPendingMigrations();
        $all = $this->getMigrationFiles();
        
        $this->output[] = "\n=== MIGRATION STATUS ===\n";
        $this->output[] = "Database: " . $this->pdo->query("SELECT DATABASE()")->fetchColumn();
        $this->output[] = "Total migrations: " . count($all);
        $this->output[] = "Applied: " . count($applied);
        $this->output[] = "Pending: " . count($pending);
        $this->output[] = "Next batch: " . $this->batch;
        
        if ($dryRun) {
            $this->output[] = "\n=== DRY RUN MODE ===\n";
        }
        
        if (!empty($pending)) {
            $this->output[] = "\nPending migrations:";
            foreach ($pending as $migration) {
                $this->output[] = "  ⏳ " . $migration;
            }
        } else {
            $this->output[] = "\n✅ No pending migrations. Database is up to date.";
        }
        
        if (!empty($applied)) {
            $this->output[] = "\nApplied migrations (last 5):";
            $stmt = $this->pdo->query("
                SELECT migration, batch, created_at 
                FROM migrations 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            while ($row = $stmt->fetch()) {
                $this->output[] = sprintf("  ✓ %s (batch %d) - %s", 
                    $row['migration'], $row['batch'], $row['created_at']);
            }
        }
    }
    
    public function migrate(bool $dryRun = false): array
    {
        $pending = $this->getPendingMigrations();
        
        if (empty($pending)) {
            $this->output[] = "No new migrations to run.";
            return $this->output;
        }
        
        $this->showStatus($dryRun);
        
        if ($dryRun) {
            $this->output[] = "\n✅ Dry run complete. No changes made.";
            return $this->output;
        }
        
        $this->output[] = "\n=== RUNNING MIGRATIONS ===\n";
        
        foreach ($pending as $migrationFile) {
            $startTime = microtime(true);
            
            try {
                // Read and execute migration
                $sql = file_get_contents($this->migrationPath . $migrationFile);
                
                // Skip if file is empty (except our init file)
                if (empty(trim($sql)) && $migrationFile !== '000_init_migrations.sql') {
                    $this->output[] = "⚠️  Warning: {$migrationFile} is empty";
                }
                
                $this->pdo->exec($sql);
                
                // Record migration
                $executionTime = round((microtime(true) - $startTime) * 1000);
                $stmt = $this->pdo->prepare("
                    INSERT INTO migrations (migration, batch, execution_time_ms) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$migrationFile, $this->batch, $executionTime]);
                
                $this->output[] = sprintf("✓ Migrated: %s (%d ms)", $migrationFile, $executionTime);
                
            } catch (\PDOException $e) {
                $this->output[] = "❌ Failed: {$migrationFile}";
                $this->output[] = "   Error: " . $e->getMessage();
                throw $e;
            }
        }
        
        $this->output[] = "\n✅ Migration batch {$this->batch} completed successfully!";
        return $this->output;
    }
    
    public function getOutput(): array
    {
        return $this->output;
    }
}