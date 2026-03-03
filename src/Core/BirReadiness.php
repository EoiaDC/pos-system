<?php
namespace App\Core;

class BirReadiness
{
    /**
     * Return readiness status array.
     */
    public static function status(): array
    {
        $pdo = db();

        // ----- Company Profile -----
        // Get the first row from company_profile (regardless of primary key name)
        $stmt = $pdo->query("SELECT * FROM company_profile LIMIT 1");
        $company = $stmt->fetch(\PDO::FETCH_ASSOC);

        $companyProfileOk = false;
        $companyMissing = [];
        if ($company) {
            // Check required fields based on your form (registered_name, tin, address)
            $requiredFields = ['registered_name', 'tin', 'address'];
            foreach ($requiredFields as $field) {
                if (empty($company[$field])) {
                    $companyMissing[] = $field;
                }
            }
            $companyProfileOk = empty($companyMissing);
        } else {
            $companyMissing[] = 'no_record';
        }

        // ----- Active Register -----
        $stmt = $pdo->query("SELECT COUNT(*) FROM pos_registers WHERE is_active = 1");
        $activeRegisterCount = (int) $stmt->fetchColumn();
        $activeRegisterOk = $activeRegisterCount > 0;

        // ----- Active OR Series (usable) -----
        $stmt = $pdo->query("
            SELECT COUNT(*) FROM or_series 
            WHERE is_active = 1 
              AND current_no BETWEEN start_no AND end_no
        ");
        $usableOrSeriesCount = (int) $stmt->fetchColumn();
        $activeOrSeriesOk = $usableOrSeriesCount > 0;

        // Overall OK
        $overallOk = $companyProfileOk && $activeRegisterOk && $activeOrSeriesOk;

        return [
            'company_profile_ok' => $companyProfileOk,
            'active_register_ok' => $activeRegisterOk,
            'active_or_series_ok' => $activeOrSeriesOk,
            'overall_ok' => $overallOk,
            'details' => [
                'company_profile' => [
                    'missing' => $companyMissing,
                ],
                'active_register' => [
                    'count' => $activeRegisterCount,
                ],
                'active_or_series' => [
                    'count' => (int) $pdo->query("SELECT COUNT(*) FROM or_series WHERE is_active = 1")->fetchColumn(),
                    'usable_count' => $usableOrSeriesCount,
                ],
            ],
        ];
    }

    /**
     * Enforce readiness: if not ready, redirect to /admin/bir-readiness with error.
     * This will be called from sales controllers later.
     */
    public static function enforceOrRedirect(): void
    {
        $status = self::status();
        if ($status['overall_ok']) {
            return;
        }

        // Not ready
        if (!isset($_SESSION['user'])) {
            header('Location: ' . APP_BASE_PATH . '/login');
            exit;
        }

        flash('error', 'BIR readiness incomplete. Complete setup before selling.');
        header('Location: ' . APP_BASE_PATH . '/admin/bir-readiness');
        exit;
    }
}