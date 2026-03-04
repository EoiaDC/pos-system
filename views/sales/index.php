<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Module</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .links { margin-top: 20px; }
        .links a { display: inline-block; margin-right: 15px; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 3px; }
        .links a:hover { background: #0056b3; }
    </style>
</head>
<body>
    
    <div class="container">
        <h1>Sales Module</h1>
        <p>Sales/POS screens will be added later.</p>

        <div class="links">
            
    <a href="<?= APP_BASE_PATH ?>/sales/start">➕ Start New Sale</a> <span style="color: #6c757d; font-size: 0.9em;">(Creates draft)</span>
    <a href="<?= APP_BASE_PATH ?>/sales/history">📋 Sales History</a> <span style="color: #6c757d; font-size: 0.9em;">(Check history)</span>
    <a href="<?= APP_BASE_PATH ?>/sales/register-status">🪙 Register Status</a> <span style="color: #6c757d; font-size: 0.9em;">(Check status)</span>
    <a href="<?= APP_BASE_PATH ?>/sales/bir-readiness">📋 BIR Readiness</a> <span style="color: #6c757d; font-size: 0.9em;">(Shows BIR requirements)</span>
</div>
        

</body>
</html>