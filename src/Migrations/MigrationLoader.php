<?php
namespace Pos\Migrations;

class MigrationLoader
{
    private string $migrationPath;
    
    public function __construct()
    {
        $this->migrationPath = __DIR__ . '/../../migrations/';
    }
    
    public function getAllMigrations(): array
    {
        $files = glob($this->migrationPath . '*.php');
        $files = array_map(function($file) {
            return basename($file);
        }, $files);
        
        sort($files);
        return $files;
    }
    
    public function loadMigration(string $filename): array
    {
        $path = $this->migrationPath . $filename;
        
        if (!file_exists($path)) {
            throw new \Exception("Migration file not found: {$filename}");
        }
        
        $migration = require $path;
        
        if (!is_array($migration) || !isset($migration['up']) || !is_callable($migration['up'])) {
            throw new \Exception("Invalid migration format: {$filename} - must return array with 'up' function");
        }
        
        // Add down placeholder if missing
        if (!isset($migration['down']) || !is_callable($migration['down'])) {
            $migration['down'] = function() {
                // Placeholder - no rollback yet
                return true;
            };
        }
        
        return $migration;
    }
}