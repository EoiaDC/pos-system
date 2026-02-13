<?php
/**
 * PH POS System - Web Migration Runner
 * WARNING: Temporary endpoint - REMOVE BEFORE PRODUCTION
 */

// Bootstrap
require_once __DIR__ . '/src/config/bootstrap.php';

use Pos\Migrations\Migrator;

$migrator = new Migrator();
$report = $migrator->up();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PH POS - Migration Runner</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #1e1e2e;
            color: #cdd6f4;
            line-height: 1.6;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #181825;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        h1 {
            color: #89b4fa;
            margin-top: 0;
            border-bottom: 2px solid #45475a;
            padding-bottom: 0.5rem;
        }
        .success {
            background: #1e3a2f;
            border-left: 4px solid #a6e3a1;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
        }
        .warning {
            background: #4a3b1c;
            border-left: 4px solid #f9e2af;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
        }
        .error {
            background: #3a1e1e;
            border-left: 4px solid #f38ba8;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 1px solid #45475a;
        }
        th {
            background: #313244;
            color: #94e2d5;
        }
        .badge {
            background: #585b70;
            color: #cdd6f4;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .timestamp {
            color: #74c7ec;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 PH POS Migration Runner</h1>
        <p><span class="badge">WARNING: Temporary Endpoint</span></p>
        
        <?php if (isset($report['message'])): ?>
            <div class="warning">
                <strong>ℹ️ <?php echo htmlspecialchars($report['message']); ?></strong>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($report['applied'])): ?>
            <div class="success">
                <strong>✅ Applied Migrations (Batch <?php echo $report['applied'][0]['batch']; ?>)</strong>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Applied At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['applied'] as $migration): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($migration['filename']); ?></code></td>
                        <td><span class="timestamp"><?php echo htmlspecialchars($migration['applied_at']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <?php if (!empty($report['errors'])): ?>
            <div class="error">
                <strong>❌ Errors Encountered</strong>
                <?php foreach ($report['errors'] as $error): ?>
                    <p style="margin-bottom: 0;">
                        <code><?php echo htmlspecialchars($error['filename']); ?>:</code><br>
                        <?php echo htmlspecialchars($error['error']); ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($report['applied']) && empty($report['errors']) && !isset($report['message'])): ?>
            <div class="warning">
                <strong>ℹ️ No migrations were applied. Check status for details.</strong>
            </div>
        <?php endif; ?>
        
        <hr style="border-color: #45475a; margin: 2rem 0 1rem;">
        
        <p style="color: #7f849c; font-size: 0.875rem; text-align: center;">
            ⚠️ This script is for development only. Remove before production deployment.<br>
            Timestamp: <?php echo date('Y-m-d H:i:s'); ?>
        </p>
    </div>
</body>
</html>