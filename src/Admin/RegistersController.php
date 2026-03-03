<?php
namespace App\Admin;

use App\Core\View;
use App\Audit\AuditEvent;
use App\Audit\Auditor;

class RegistersController
{
    public static function index()
    {
        $pdo = db();
        $stmt = $pdo->query("SELECT * FROM pos_registers ORDER BY id DESC");
        $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return View::render('admin/registers', ['registers' => $registers]);
    }

    public static function create()
    {
        $registerCode = $_POST['register_code'] ?? '';
        $machineName = $_POST['machine_name'] ?? '';
        $serialNo = $_POST['serial_no'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($registerCode)) {
            flash('error', 'Register code is required.');
            header('Location: ' . APP_BASE_PATH . '/admin/registers');
            exit;
        }

        $pdo = db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("INSERT INTO pos_registers (register_code, machine_name, serial_no, is_active, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$registerCode, $machineName, $serialNo, $isActive]);
            $registerId = $pdo->lastInsertId();

            // Audit log
            $event = new AuditEvent('bir.register.created', 'pos_register');
            $event->actor_user_id = $_SESSION['user']['id'] ?? null;
            $event->entity_id = $registerId;
            $event->meta = [
                'register_code' => $registerCode,
                'machine_name' => $machineName,
                'serial_no' => $serialNo,
                'is_active' => $isActive
            ];
            Auditor::record($event);

            $pdo->commit();
            flash('success', 'Register added.');
        } catch (\Exception $e) {
            $pdo->rollBack();
            flash('error', 'Error adding register: ' . $e->getMessage());
        }

        header('Location: ' . APP_BASE_PATH . '/admin/registers');
        exit;
    }

    public static function toggle()
    {
        $registerId = $_POST['register_id'] ?? 0;
        $newStatus = (int)($_POST['is_active'] ?? 0); // 0 or 1

        if (!$registerId) {
            flash('error', 'Invalid register.');
            header('Location: ' . APP_BASE_PATH . '/admin/registers');
            exit;
        }

        $pdo = db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("UPDATE pos_registers SET is_active = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newStatus, $registerId]);

            // Audit log
            $event = new AuditEvent('bir.register.updated', 'pos_register');
            $event->actor_user_id = $_SESSION['user']['id'] ?? null;
            $event->entity_id = $registerId;
            $event->meta = ['is_active' => $newStatus];
            Auditor::record($event);

            $pdo->commit();
            flash('success', 'Register status updated.');
        } catch (\Exception $e) {
            $pdo->rollBack();
            flash('error', 'Error updating register: ' . $e->getMessage());
        }

        header('Location: ' . APP_BASE_PATH . '/admin/registers');
        exit;
    }
}