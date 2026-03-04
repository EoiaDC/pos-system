<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIR Readiness Checklist</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 25px; }
        .checklist { margin: 20px 0; }
        .checklist-item { padding: 15px; margin: 10px 0; border-radius: 5px; display: flex; align-items: center; }
        .checklist-item.pass { background: #d4edda; border-left: 5px solid #28a745; }
        .checklist-item.fail { background: #f8d7da; border-left: 5px solid #dc3545; }
        .status-icon { font-size: 24px; margin-right: 15px; width: 30px; text-align: center; }
        .status-text { flex: 1; font-weight: bold; }
        .status-message { color: #666; margin-left: 45px; font-size: 0.9em; }
        .overall { padding: 20px; border-radius: 5px; text-align: center; margin: 20px 0; font-size: 1.2em; font-weight: bold; }
        .overall.ready { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .overall.not-ready { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .missing-list { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .missing-list ul { margin: 10px 0 0 20px; }
        .back-link { display: inline-block; margin-top: 20px; padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 3px; }
        .back-link:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 BIR Readiness Checklist</h1>
        <p>System compliance status for BIR requirements</p>
        
        <?php
        // Get readiness status
        require_once __DIR__ . '/../../src/Sales/BirReadiness.php';
        use POS\Sales\BirReadiness;
        
        $status = BirReadiness::getReadinessStatus();
        $missing = BirReadiness::getMissingRequirements();
        ?>
        
        <div class="checklist">
            <div class="checklist-item <?= $status['company_profile_complete'] ? 'pass' : 'fail' ?>">
                <span class="status-icon"><?= $status['company_profile_complete'] ? '✅' : '❌' ?></span>
                <span class="status-text">Company Profile</span>
                <span><?= $status['company_profile_complete'] ? 'Complete' : 'Incomplete' ?></span>
            </div>
            <?php if (!$status['company_profile_complete']): ?>
            <div class="status-message">→ Admin must complete company profile in BIR Setup</div>
            <?php endif; ?>
            
            <div class="checklist-item <?= $status['has_active_register'] ? 'pass' : 'fail' ?>">
                <span class="status-icon"><?= $status['has_active_register'] ? '✅' : '❌' ?></span>
                <span class="status-text">Active POS Register</span>
                <span><?= $status['has_active_register'] ? 'Yes' : 'None' ?></span>
            </div>
            <?php if (!$status['has_active_register']): ?>
            <div class="status-message">→ Admin must activate at least one register</div>
            <?php endif; ?>
            
            <div class="checklist-item <?= $status['has_active_or_series'] ? 'pass' : 'fail' ?>">
                <span class="status-icon"><?= $status['has_active_or_series'] ? '✅' : '❌' ?></span>
                <span class="status-text">Active OR Series</span>
                <span><?= $status['has_active_or_series'] ? 'Yes' : 'None' ?></span>
            </div>
            <?php if (!$status['has_active_or_series']): ?>
            <div class="status-message">→ Admin must create and activate an OR series</div>
            <?php endif; ?>
        </div>
        
        <div class="overall <?= $status['ready_for_sales'] ? 'ready' : 'not-ready' ?>">
            <?php if ($status['ready_for_sales']): ?>
                ✅ SYSTEM READY FOR SALES — All BIR requirements met
            <?php else: ?>
                ⚠️ SYSTEM NOT READY — BIR requirements missing
            <?php endif; ?>
        </div>
        
        <?php if (!empty($missing)): ?>
        <div class="missing-list">
            <strong>Missing Requirements:</strong>
            <ul>
                <?php foreach ($missing as $req): ?>
                <li><?= htmlspecialchars($req) ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-top: 10px;">Please contact administrator to complete BIR setup.</p>
        </div>
        <?php endif; ?>
        
        <a href="<?= APP_BASE_PATH ?>/sales" class="back-link">← Back to Sales</a>
    </div>
</body>
</html>