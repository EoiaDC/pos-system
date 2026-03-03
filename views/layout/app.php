<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Display flash messages if Response class exists
        if (class_exists('\\POS\\Core\\Response')) {
            $errorFlash = \POS\Core\Response::getFlash('error');
            $successFlash = \POS\Core\Response::getFlash('success');
            
            if ($errorFlash): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px;">
                    <strong>Error:</strong> <?= htmlspecialchars($errorFlash) ?>
                </div>
            <?php endif;
            
            if ($successFlash): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 4px;">
                    <strong>Success:</strong> <?= htmlspecialchars($successFlash) ?>
                </div>
            <?php endif;
        }
        ?>
        
        <div style="padding:16px;border-bottom:1px solid #ccc;">
        <strong><?= htmlspecialchars($app['name'] ?? 'POS') ?></strong>
        <nav style="margin-left:12px; display:inline;">
            <a href="<?= APP_BASE_PATH ?>/">Home</a> |
            <?php if (Auth::check()): ?>
                <?php if (Auth::hasPermission('sales.view')): ?>
                    <a href="<?= APP_BASE_PATH ?>/sales">Sales</a> |
                <?php endif; ?>
                <?php if (Auth::hasPermission('inventory.view')): ?>
                    <a href="<?= APP_BASE_PATH ?>/inventory">Inventory</a> |
                <?php endif; ?>
                <?php if (Auth::hasPermission('purchasing.view')): ?>
                    <a href="<?= APP_BASE_PATH ?>/purchasing">Purchasing</a> |
                <?php endif; ?>
                <?php if (Auth::hasPermission('admin.access')): ?>
                    <a href="<?= APP_BASE_PATH ?>/admin">Admin</a> |
                <?php endif; ?>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="<?= APP_BASE_PATH ?>/logout" method="POST" style="display: none;"></form>
            <?php else: ?>
                <a href="<?= APP_BASE_PATH ?>/login">Login</a>
            <?php endif; ?>
        </nav>
    </div>

    <main style="padding:16px;">
        <?php
        $success = flash('success');
        $error = flash('error');
        $info = flash('info');
        ?>
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 10px; border: 1px solid #c3e6cb;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 10px; border: 1px solid #f5c6cb;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div style="background-color: #d1ecf1; color: #0c5460; padding: 10px; margin-bottom: 10px; border: 1px solid #bee5eb;">
                <?= htmlspecialchars($info) ?>
            </div>
        <?php endif; ?>

        <?php if (Auth::hasPermission('bir.readiness.view')): ?>
            <a href="<?= APP_BASE_PATH ?>/admin/bir-readiness">BIR Readiness</a> |
        <?php endif; ?>

        <?php if (Auth::hasPermission('sales.payments.view')): ?>
            <a href="<?= APP_BASE_PATH ?>/sales/payments">Payments</a>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <div style="padding:16px;border-top:1px solid #ccc;font-size:12px;color:#666;">
        Core infrastructure skeleton (Day 1)
    </div>
</div>
</body>
</html>