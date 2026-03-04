<?php
namespace App\Core;

use PDO;

/**
 * BIR Readiness Checker
 * 
 * Ensures all BIR requirements are met before allowing sales transactions
 * Checks company profile, active register, and active OR series
 */
class BirReadiness
{
    private static ?PDO $db = null;
    
    /**
     * Initialize database connection
     */
    private static function initDb(): void
    {
        if (self::$db === null) {
            $config = require __DIR__ . '/../../config/database.php';
            self::$db = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                $config['user'],
                $config['pass']
            );
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }
    
    /**
     * Check if company profile is complete
     */
    public static function isCompanyProfileComplete(): bool
    {
        self::initDb();
        
        $stmt = self::$db->query("SELECT COUNT(*) FROM company_profile");
        $count = $stmt->fetchColumn();
        
        return $count > 0;
    }
    
    /**
     * Check if at least one active register exists
     */
    public static function hasActiveRegister(): bool
    {
        self::initDb();
        
        $stmt = self::$db->query("SELECT COUNT(*) FROM pos_registers WHERE is_active = 1");
        $count = $stmt->fetchColumn();
        
        return $count > 0;
    }
    
    /**
     * Check if at least one active OR series exists
     */
    public static function hasActiveOrSeries(): bool
    {
        self::initDb();
        
        $stmt = self::$db->query("SELECT COUNT(*) FROM or_series WHERE is_active = 1");
        $count = $stmt->fetchColumn();
        
        return $count > 0;
    }
    
    /**
     * Get complete readiness status as array
     */
    public static function getReadinessStatus(): array
    {
        return [
            'company_profile_ok' => self::isCompanyProfileComplete(),
            'has_active_register' => self::hasActiveRegister(),
            'has_active_or_series' => self::hasActiveOrSeries(),
            'overall_ok' => self::isCompanyProfileComplete() && 
                           self::hasActiveRegister() && 
                           self::hasActiveOrSeries()
        ];
    }
    
    /**
     * Check if system is ready for sales
     */
    public static function isReadyForSales(): bool
    {
        return self::isCompanyProfileComplete() && 
               self::hasActiveRegister() && 
               self::hasActiveOrSeries();
    }
    
    /**
     * Get list of missing requirements
     */
    public static function getMissingRequirements(): array
    {
        $missing = [];
        
        if (!self::isCompanyProfileComplete()) {
            $missing[] = 'Company profile must be completed';
        }
        if (!self::hasActiveRegister()) {
            $missing[] = 'At least one active POS register required';
        }
        if (!self::hasActiveOrSeries()) {
            $missing[] = 'At least one active OR series required';
        }
        
        return $missing;
    }
    
    /**
     * Enforce BIR readiness or redirect to admin page
     */
    public static function enforceOrRedirect(): void
    {
        if (!self::isReadyForSales()) {
            $_SESSION['flash']['error'] = 'BIR readiness incomplete. Complete setup before selling.';
            header('Location: /pos-system/public/admin/bir-readiness');
            exit;
        }
    }
}