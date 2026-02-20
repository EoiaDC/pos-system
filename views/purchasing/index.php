<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchasing Module</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #1e1e2e;
            color: #cdd6f4;
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
        .modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .module-card {
            background: #313244;
            padding: 1.5rem;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #45475a;
        }
        .module-card h3 {
            color: #94e2d5;
            margin: 0 0 0.5rem 0;
        }
        .note {
            background: #4a3b1c;
            border-left: 4px solid #f9e2af;
            padding: 1rem;
            margin: 1rem 0;
            color: #cdd6f4;
        }
        .badge {
            background: #585b70;
            color: #cdd6f4;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            display: inline-block;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚚 Purchasing Module</h1>
        
        <div class="modules">
            <div class="module-card">
                <h3>📋 Suppliers</h3>
                <p>Manage supplier information</p>
                <span class="badge">Coming Soon</span>
            </div>
            
            <div class="module-card">
                <h3>📦 Purchase Orders</h3>
                <p>Create and manage POs</p>
                <span class="badge">Coming Soon</span>
            </div>
            
            <div class="module-card">
                <h3>📬 Deliveries</h3>
                <p>Receive and verify shipments</p>
                <span class="badge">Coming Soon</span>
            </div>
        </div>
        
        <div class="note">
            <strong>📝 Note:</strong> Receiving/stock effects will be implemented later.
        </div>
        
        <p style="color: #7f849c; text-align: center; font-size: 0.875rem;">
            PH POS System - Purchasing Module v0.1
        </p>
    </div>
</body>
</html>