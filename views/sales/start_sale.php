<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start New Sale - POS System</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .info-box { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 3px; }
        .form-group { margin: 20px 0; text-align: center; }
        .btn { padding: 12px 30px; font-size: 1.1em; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; text-decoration: none; display: inline-block; padding: 8px 15px; }
        .btn-secondary:hover { background: #5a6268; }
        .flash-error { background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Start New Sale</h1>
        
        <?php
        // Display flash messages if any
        if (class_exists('\\POS\\Core\\Response')) {
            $errorFlash = \POS\Core\Response::getFlash('error');
            if ($errorFlash): ?>
                <div class="flash-error">
                    <strong>Error:</strong> <?= htmlspecialchars($errorFlash) ?>
                </div>
            <?php endif;
        }
        ?>
        
        <div class="info-box">
            <strong>📌 BIR Compliance Notice:</strong>
            <p>Before starting a sale, the system has verified:</p>
            <ul>
                <li>✅ Company Profile is complete</li>
                <li>✅ Active POS Register exists</li>
                <li>✅ Active OR Series is configured</li>
            </ul>
            <p>This sale will be created as a <strong>DRAFT</strong>. No OR will be issued yet.</p>
        </div>
        
        <div class="form-group">
            <form method="POST" action="/sales/start">
                <button type="submit" class="btn btn-primary">➕ Create Draft Sale</button>
            </form>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?= APP_BASE_PATH ?>/sales" class="btn-secondary">← Back to Sales Module</a>
        </div>
    </div>
</body>
</html>