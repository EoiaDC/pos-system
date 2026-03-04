<?php

namespace POS\Sales;

use App\Core\Auth;
use App\Core\Response;  // Not used here but good practice
use App\Core\Validator; // Not used here but good practice
use App\Core\BirReadiness; // Not used here but good practice

/**
 * Sales Audit Helper
 * 
 * Logs all sales-related events to audit_logs table
 * Ensures BIR-compliant audit trail
 */
class SalesAudit
{
    private static ?\PDO $db = null;
    
    /**
     * Initialize database connection
     */
    private static function initDb(): void
    {
        if (self::$db === null) {
            $config = require __DIR__ . '/../../config/database.php';
            self::$db = new \PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                $config['user'],
                $config['pass']
            );
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }
    
    /**
     * Log a sales event to audit_logs
     * 
     * @param string $event Event type (sale.started, sale.voided, etc.)
     * @param int|null $salesHeaderId Related sales header ID
     * @param array $metadata Additional data to store
     */
    public static function log(string $event, ?int $salesHeaderId = null, array $metadata = []): void
    {
        self::initDb();
        
        $userId = Auth::userId() ?? 0;
        $username = Auth::user()['username'] ?? 'system';
        
        $metadata['event'] = $event;
        $metadata['sales_header_id'] = $salesHeaderId;
        $metadata['timestamp'] = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO audit_logs (
            user_id, username, action, table_name, record_id, new_data, ip_address, user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            $userId,
            $username,
            $event,
            'sales_headers',
            $salesHeaderId,
            json_encode($metadata),
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    /**
     * Log sale started event (draft creation)
     */
    public static function logSaleStarted(int $salesHeaderId, array $details = []): void
    {
        self::log('sale.started', $salesHeaderId, $details);
    }
    
    /**
     * Log sale voided event
     */
    public static function logSaleVoided(int $salesHeaderId, string $reason, array $details = []): void
    {
        $details['reason'] = $reason;
        self::log('sale.voided', $salesHeaderId, $details);
    }
    
    /**
     * Log sale completed event
     */
    public static function logSaleCompleted(int $salesHeaderId, array $details = []): void
    {
        self::log('sale.completed', $salesHeaderId, $details);
    }
    
    /**
     * Log OR assigned event
     */
    public static function logOrAssigned(int $salesHeaderId, string $orNumber, array $details = []): void
    {
        $details['or_number'] = $orNumber;
        self::log('sale.or_assigned', $salesHeaderId, $details);
    }
}