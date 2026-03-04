<?php

require_once __DIR__ . '/../Database/DB.php';
require_once __DIR__ . '/MigrationLoader.php';

// Add this use statement
use Pos\Database\DB;

class Migrator
{
    private string $migrationsPath;

    public function __construct(string $migrationsPath)
    {
        $this->migrationsPath = $migrationsPath;
    }

    public function ensureMigrationsTable(): void
    {
        DB::execute("
            CREATE TABLE IF NOT EXISTS schema_migrations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL UNIQUE,
                batch INT NOT NULL,
                applied_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    private function nextBatch(): int
    {
        $row = DB::fetch("SELECT MAX(batch) AS max_batch FROM schema_migrations");
        $max = $row && $row['max_batch'] !== null ? (int)$row['max_batch'] : 0;
        return $max + 1;
    }

    private function appliedFilenames(): array
    {
        $rows = DB::fetchAll("SELECT filename FROM schema_migrations");
        $set = [];
        foreach ($rows as $r) $set[$r['filename']] = true;
        return $set;
    }

    public function up(): array
    {
        $this->ensureMigrationsTable();

        $loader = new MigrationLoader();
        $files = $loader->listMigrationFiles($this->migrationsPath);

        $applied = $this->appliedFilenames();
        $batch = $this->nextBatch();

        $appliedNow = [];

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            if (isset($applied[$filename])) continue;

            $migration = $loader->load($filePath);

            DB::transaction(function () use ($migration, $filename, $batch, &$appliedNow) {
                // run up
                $migration['up']();

                // record
                DB::execute(
                    "INSERT INTO schema_migrations (filename, batch, applied_at) VALUES (?, ?, ?)",
                    [$filename, $batch, date('Y-m-d H:i:s')]
                );

                $appliedNow[] = $filename;
            });
        }

        return $appliedNow;
    }
}