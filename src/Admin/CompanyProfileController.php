<?php
namespace App\Admin;

use App\Core\View;
use App\Audit\AuditEvent;
use App\Audit\Auditor;

class CompanyProfileController
{
    public static function index()
    {
        $pdo = db();
        // Assume single record with id=1
        $stmt = $pdo->query("SELECT * FROM company_profile WHERE id = 1");
        $profile = $stmt->fetch(\PDO::FETCH_ASSOC);
        return View::render('admin/company_profile', ['profile' => $profile]);
    }

    public static function update()
    {
        $data = [
            'registered_name' => $_POST['registered_name'] ?? '',
            'trade_name' => $_POST['trade_name'] ?? '',
            'tin' => $_POST['tin'] ?? '',
            'address' => $_POST['address'] ?? '',
            // 'vat_type' => $_POST['vat_type'] ?? '', // if column exists
        ];

        // Basic validation
        if (empty($data['registered_name']) || empty($data['tin'])) {
            flash('error', 'Company name and TIN are required.');
            header('Location: ' . APP_BASE_PATH . '/admin/company-profile');
            exit;
        }

        $pdo = db();
        $pdo->beginTransaction();

        try {
            // Check if record exists
            $stmt = $pdo->query("SELECT id FROM company_profile WHERE id = 1");
            $exists = $stmt->fetch();

            if ($exists) {
                // Update
                $sql = "UPDATE company_profile SET 
                        registered_name = :registered_name,
                        trade_name = :trade_name,
                        tin = :tin,
                        address = :address,
                        updated_at = NOW()
                        WHERE id = 1";
            } else {
                // Insert
                $sql = "INSERT INTO company_profile 
                        (registered_name, trade_name, tin, address, created_at, updated_at)
                        VALUES 
                        (:registered_name, :trade_name, :tin, :address, NOW(), NOW())";
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);

            // Audit log
            $event = new AuditEvent('bir.company_profile.updated', 'company_profile');
            $event->actor_user_id = $_SESSION['user']['id'] ?? null;
            $event->entity_id = 1;
            $event->meta = $data;
            Auditor::record($event);

            $pdo->commit();
            flash('success', 'Company profile saved.');
        } catch (\Exception $e) {
            $pdo->rollBack();
            flash('error', 'Error saving profile: ' . $e->getMessage());
        }

        header('Location: ' . APP_BASE_PATH . '/admin/company-profile');
        exit;
    }
}