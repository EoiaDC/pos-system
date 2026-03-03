<?php
namespace App\Audit;
require_once __DIR__ . '/AuditEvent.php';

class Auditor
{
    public static function record(AuditEvent $event): void
    {
        $pdo = db();
        $sql = "INSERT INTO audit_logs 
                (actor_user_id, action, entity_type, entity_id, meta_json, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $event->actor_user_id,
            $event->action,
            $event->entity_type,
            $event->entity_id,
            json_encode($event->meta),
            $event->ip_address,
            $event->user_agent,
            $event->created_at
        ]);
    }

    /**
     * Simple audit log helper.
     * @param string $action Event code (e.g., 'sale.register.selected')
     * @param array $meta Optional metadata (will be merged with defaults)
     */
    public static function log(string $action, array $meta = []): void
    {
        $event = new AuditEvent($action, 'system'); // entity_type can be overridden via meta if needed
        $event->actor_user_id = $_SESSION['user']['id'] ?? null;
        $event->ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $event->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $event->created_at = date('Y-m-d H:i:s');

        // Merge meta, allowing override of entity_type, entity_id, etc.
        foreach ($meta as $key => $value) {
            if (property_exists($event, $key)) {
                $event->$key = $value;
            } else {
                $event->meta[$key] = $value;
            }
        }

        self::record($event);
    }

}