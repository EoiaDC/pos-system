<?php

class MigrationLoader
{
    /**
     * List all migration files in the given path
     */
    public function listMigrationFiles(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }
        
        $files = glob($path . '/*.php');
        sort($files); // Run in chronological order
        return $files;
    }
    
    /**
     * Load a migration file and return a standardized array with 'up' and 'down' callables
     * 
     * @throws Exception if migration format is invalid
     */
    public function load(string $filePath): array
    {
        $migration = require $filePath;
        
        // CASE 1: Class-based migration (our new standard format)
        if (is_object($migration) && method_exists($migration, 'up')) {
            return [
                'up' => function($db) use ($migration) {
                    $migration->up($db);
                },
                'down' => function($db) use ($migration) {
                    if (method_exists($migration, 'down')) {
                        $migration->down($db);
                    }
                }
            ];
        }
        
        // CASE 2: Array-based migration (old format)
        if (is_array($migration) && isset($migration['up'])) {
            return $migration;
        }
        
        // CASE 3: Invalid format
        throw new Exception("Invalid migration format in: $filePath");
    }
    
    /**
     * Get the migration name from file path
     */
    public function getMigrationName(string $filePath): string
    {
        return basename($filePath);
    }
}