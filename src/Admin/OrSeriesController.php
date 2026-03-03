<?php
namespace App\Admin;

use App\Core\View;
use App\Audit\AuditEvent;
use App\Audit\Auditor;

class OrSeriesController
{
    public static function index()
    {
        $pdo = db();
        $stmt = $pdo->query("SELECT * FROM or_series ORDER BY id DESC");
        $series = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get registers for dropdown (if register_id column exists)
        $registers = [];
        $stmt = $pdo->query("SELECT id, register_code FROM pos_registers WHERE is_active = 1 ORDER BY register_code");
        $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return View::render('admin/or_series', [
            'series' => $series,
            'registers' => $registers
        ]);
    }

    public static function create()
    {
        $seriesCode = $_POST['series_code'] ?? '';
        $startNo = $_POST['start_no'] ?? 0;
        $endNo = $_POST['end_no'] ?? 0;
        $currentNo = $_POST['start_no'] ?? 0; // default to start_no
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $registerId = $_POST['register_id'] ?? null; // if column exists

        if (empty($seriesCode) || $startNo <= 0 || $endNo <= 0 || $startNo > $endNo) {
            flash('error', 'Invalid series data. Check start/end numbers.');
            header('Location: ' . APP_BASE_PATH . '/admin/or-series');
            exit;
        }

        $pdo = db();
        $pdo->beginTransaction();

        try {
            // Insert OR series
            $sql = "INSERT INTO or_series (series_code, start_no, end_no, current_no, is_active, created_at";
            $values = "VALUES (?, ?, ?, ?, ?, NOW()";
            $params = [$seriesCode, $startNo, $endNo, $currentNo, $isActive];

            if ($registerId) {
                $sql .= ", register_id";
                $values .= ", ?";
                $params[] = $registerId;
            }
            $sql .= ") " . $values . ")";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $seriesId = $pdo->lastInsertId();

            // Audit log
            $event = new AuditEvent('bir.or_series.created', 'or_series');
            $event->actor_user_id = $_SESSION['user']['id'] ?? null;
            $event->entity_id = $seriesId;
            $event->meta = [
                'series_code' => $seriesCode,
                'start_no' => $startNo,
                'end_no' => $endNo,
                'current_no' => $currentNo,
                'is_active' => $isActive,
                'register_id' => $registerId
            ];
            Auditor::record($event);

            $pdo->commit();
            flash('success', 'OR series created.');
        } catch (\Exception $e) {
            $pdo->rollBack();
            flash('error', 'Error creating series: ' . $e->getMessage());
        }

        header('Location: ' . APP_BASE_PATH . '/admin/or-series');
        exit;
    }
}