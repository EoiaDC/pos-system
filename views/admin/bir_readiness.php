<h1>BIR Readiness Checklist</h1>

<?php
$status = $status ?? []; // ensure variable exists
$companyOk = $status['company_profile_ok'] ?? false;
$registerOk = $status['active_register_ok'] ?? false;
$orOk = $status['active_or_series_ok'] ?? false;
$overallOk = $status['overall_ok'] ?? false;
$details = $status['details'] ?? [];
?>

<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">
    <tr>
        <th>Check</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <tr>
        <td>Company Profile</td>
        <td style="color: <?= $companyOk ? 'green' : 'red' ?>; font-weight: bold;">
            <?= $companyOk ? 'OK' : 'NOT OK' ?>
        </td>
        <td>
            <?php if (!$companyOk): ?>
                <a href="<?= APP_BASE_PATH ?>/admin/company-profile">Fix Company Profile</a>
            <?php else: ?>
                ✓
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>Active POS Register</td>
        <td style="color: <?= $registerOk ? 'green' : 'red' ?>; font-weight: bold;">
            <?= $registerOk ? 'OK' : 'NOT OK' ?>
            <?php if (!$registerOk): ?>
                <br><small>(<?= $details['active_register']['count'] ?? 0 ?> active registers)</small>
            <?php endif; ?>
        </td>
        <td>
            <?php if (!$registerOk): ?>
                <a href="<?= APP_BASE_PATH ?>/admin/registers">Manage Registers</a>
            <?php else: ?>
                ✓
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>Active & Usable OR Series</td>
        <td style="color: <?= $orOk ? 'green' : 'red' ?>; font-weight: bold;">
            <?= $orOk ? 'OK' : 'NOT OK' ?>
            <?php if (!$orOk): ?>
                <br><small>(<?= $details['active_or_series']['usable_count'] ?? 0 ?> usable out of <?= $details['active_or_series']['count'] ?? 0 ?> active)</small>
            <?php endif; ?>
        </td>
        <td>
            <?php if (!$orOk): ?>
                <a href="<?= APP_BASE_PATH ?>/admin/or-series">Manage OR Series</a>
            <?php else: ?>
                ✓
            <?php endif; ?>
        </td>
    </tr>
</table>

<h2>Overall Status: <span style="color: <?= $overallOk ? 'green' : 'red' ?>;"><?= $overallOk ? 'READY' : 'NOT READY' ?></span></h2>

<p><a href="<?= APP_BASE_PATH ?>/admin">Back to Admin Dashboard</a></p>