<?php
namespace Pos\Migrations;

use Pos\Database\DB;

class Migrator
{
    private \PDO $pdo;
    private MigrationLoader $loader;
    private array $report = [];
    
    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->loader = new MigrationLoader();
        $this->ensureMigrationTableExists();
    }
    
    private function ensureMigrationTableExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `schema_migrations` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `filename` VARCHAR(255) NOT NULL UNIQUE,
                `batch` INT NOT NULL,
                `applied_at` DATETIME NOT NULL,
                INDEX `idx_batch` (`batch`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $this->pdo->exec($sql);
    }
    
    public function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT filename FROM schema_migrations ORDER BY filename");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    private function getNextBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT COALESCE(MAX(batch), 0) + 1 FROM schema_migrations");
        return (int) $stmt->fetchColumn();
    }
    
    public function getPendingMigrations(): array
    {
        $applied = $this->getAppliedMigrations();
        $all = $this->loader->getAllMigrations();
        
        return array_values(array_diff($all, $applied));
    }
    
    public function up(): array
{
    $this->report = [
        'applied' => [],
        'skipped' => [],
        'errors' => []
    ];
    
    $pending = $this->getPendingMigrations();
    
    if (empty($pending)) {
        $this->report['message'] = 'No pending migrations to apply.';
        return $this->report;
    }
    
    $batch = $this->getNextBatchNumber();
    $now = date('Y-m-d H:i:s');
    
    foreach ($pending as $filename) {
        try {
            $migration = $this->loader->loadMigration($filename);
            
            // Execute the up function (no transaction for now)
            $result = $migration['up']($this->pdo);
            
            // Record the migration
            $stmt = $this->pdo->prepare(
                "INSERT INTO schema_migrations (filename, batch, applied_at) 
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$filename, $batch, $now]);
            
            $this->report['applied'][] = [
                'filename' => $filename,
                'batch' => $batch,
                'applied_at' => $now
            ];
            
        } catch (\Exception $e) {
            $this->report['errors'][] = [
                'filename' => $filename,
                'error' => $e->getMessage()
            ];
            break;
        }
    }
    
    return $this->report;
}
}