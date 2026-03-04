<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers - Purchasing Module</title>
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
        .coming-soon {
            background: #313244;
            padding: 2rem;
            border-radius: 6px;
            text-align: center;
            margin: 2rem 0;
            border: 2px dashed #f9e2af;
        }
        .coming-soon h2 {
            color: #f9e2af;
            margin: 0 0 1rem 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: #89b4fa;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: #313244;
            border-radius: 4px;
            border: 1px solid #45475a;
        }
        .back-link:hover {
            background: #45475a;
        }
        .note {
            background: #4a3b1c;
            border-left: 4px solid #f9e2af;
            padding: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Suppliers</h1>
        
        <div class="coming-soon">
            <h2>✨ Coming Soon</h2>
            <p>The suppliers management page is under construction.</p>
            <p>Future features will include:</p>
            <ul style="list-style: none; padding: 0; color: #a6e3a1;">
                <li>✓ Supplier list with search/filter</li>
                <li>✓ Add/edit supplier information</li>
                <li>✓ Supplier contact management</li>
                <li>✓ Purchase history by supplier</li>
            </ul>
        </div>
        
        <div class="note">
            <strong>📝 Note:</strong> Supplier management will include TIN, address, phone, email fields as defined in the database schema.
        </div>
        
        <a href="/purchasing" class="back-link">← Back to Purchasing Module</a>
    </div>
</body>
</html>