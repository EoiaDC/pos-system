<?php
namespace App\Core;

use App\Audit\AuditEvent;
use App\Audit\Auditor;

class OrIssuanceService
{
    /**
     * Issue an OR number for a posted sale.
     * @param int $saleId
     * @param int $actorUserId
     * @return int issued OR number, or 0 on failure.
     * @throws \Exception if preconditions fail.
     */
    public static function issueForSale(int $saleId, int $actorUserId): int
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            // Lock sale header row
            $stmt = $pdo->prepare("SELECT id, status, or_no, or_series_id FROM sales_headers WHERE id = ? FOR UPDATE");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$sale) {
                throw new \Exception("Sale not found.");
            }
            if ($sale['status'] !== 'POSTED') {
                throw new \Exception("Sale is not POSTED.");
            }
            if ($sale['or_no'] !== null) {
                throw new \Exception("OR already issued for this sale.");
            }
            if (!$sale['or_series_id']) {
                throw new \Exception("No OR series selected for this sale.");
            }

            // Lock OR series row
            $stmt = $pdo->prepare("SELECT id, start_no, end_no, current_no FROM or_series WHERE id = ? AND is_active = 1 FOR UPDATE");
            $stmt->execute([$sale['or_series_id']]);
            $series = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$series) {
                throw new \Exception("OR series not found or inactive.");
            }

            // Validate current within range
            if ($series['current_no'] < $series['start_no'] || $series['current_no'] > $series['end_no']) {
                throw new \Exception("Current OR number out of range.");
            }

            $issuedNo = $series['current_no'];

            // Update sale header
            $updateSale = $pdo->prepare("UPDATE sales_headers SET or_no = ?, updated_at = NOW() WHERE id = ?");
            $updateSale->execute([$issuedNo, $saleId]);

            // Increment series current_no
            $newCurrent = $series['current_no'] + 1;
            $updateSeries = $pdo->prepare("UPDATE or_series SET current_no = ? WHERE id = ?");
            $updateSeries->execute([$newCurrent, $series['id']]);

            // Audit log
            $event = new AuditEvent('sale.or_issued', 'sales_header');
            $event->actor_user_id = $actorUserId;
            $event->entity_id = $saleId;
            $event->meta = [
                'sale_id' => $saleId,
                'or_series_id' => $series['id'],
                'issued_or_no' => $issuedNo,
                'previous_current_no' => $series['current_no'],
                'new_current_no' => $newCurrent,
                'note' => 'OR reserved/assigned (no print yet)'
            ];
            Auditor::record($event);

            $pdo->commit();
            return $issuedNo;

        } catch (\Exception $e) {
            $pdo->rollBack();
            // Log error or rethrow
            error_log("OR Issuance failed for sale $saleId: " . $e->getMessage());
            return 0; // failure
        }
    }
}