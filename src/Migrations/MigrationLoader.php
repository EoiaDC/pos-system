<?php

class MigrationLoader
{
    public function listMigrationFiles(string $path): array
    {
        $files = glob($path . '/*.php');
        sort($files);
        return $files;
    }
    
    public function load(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new Exception("Migration file not found: $filePath");
        }
        
        $migration = require $filePath;
        
        if (!is_array($migration) || !isset($migration['up'])) {
            throw new Exception("Invalid migration format in: $filePath");
        }
        
        return $migration;
    }
}