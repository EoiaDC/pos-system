<?php
namespace App\Core;

use App\Audit\AuditEvent;
use App\Audit\Auditor;

class SalesDraftService
{
    /**
     * Create a draft sale header.
     * Returns inserted sale ID, or 0 if BIR readiness fails.
     */
    public static function createDraft(int $actorUserId): int
    {
        // Check BIR readiness
        $status = BirReadiness::status();
        if (!$status['overall_ok']) {
            return 0;
        }

        $pdo = db();
        $sql = "INSERT INTO sales_headers (status, created_by, created_at) VALUES ('DRAFT', ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$actorUserId]);
        $saleId = (int) $pdo->lastInsertId();

        // Record audit event
        $event = new AuditEvent('sale.started', 'sales_header');
        $event->actor_user_id = $actorUserId;
        $event->entity_id = $saleId;
        $event->meta = [
            'sale_id' => $saleId,
            'status' => 'DRAFT',
            'note' => 'draft header created; no OR issued'
        ];
        Auditor::record($event);

        return $saleId;
    }
}