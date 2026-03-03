<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - POS System</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .info-box { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 3px; }
        .sale-id { background: #f0f0f0; padding: 10px; border-radius: 3px; font-family: monospace; margin: 10px 0; }
        .back-link { display: inline-block; margin-top: 20px; padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 3px; }
        .back-link:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Shopping Cart (Coming Soon)</h1>
        
        <div class="info-box">
            <strong>📌 Draft Sale Created</strong>
            <p>Your sale has been started and is now in draft status.</p>
        </div>
        
        <?php if (isset($_GET['id'])): ?>
        <div class="sale-id">
            Sale ID: <strong><?= htmlspecialchars($_GET['id']) ?></strong>
        </div>
        <?php endif; ?>
        
        <p>This page will contain the actual POS cart interface:</p>
        <ul>
            <li>Item search and scanning</li>
            <li>Quantity adjustments</li>
            <li>Discounts</li>
            <li>Payment processing</li>
            <li>OR generation</li>
        </ul>
        
        <p><em>Full cart functionality coming in Day 3.</em></p>
        
        <a href="/sales" class="back-link">← Back to Sales Module</a>
    </div>
</body>
</html>