<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movement - Coming Soon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            color: #333;
        }

        .coming-soon {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #0066cc;
        }

        .features {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 10px;
            margin: 8px 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 3px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .emoji {
            font-size: 1.3em;
            margin-right: 10px;
        }

        .back-link {
            margin-top: 30px;
        }

        .back-link a {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="coming-soon">
            <h1>📊 Stock Movement (Coming Soon)</h1>
        </div>

        <div class="features">
            <h2>Planned Features:</h2>
            <ul>
                <li><span class="emoji">📦</span> Receiving from Purchase Orders</li>
                <li><span class="emoji">🛒</span> Sales deduction</li>
                <li><span class="emoji">⚖️</span> Adjustments (stock count, corrections)</li>
                <li><span class="emoji">📋</span> Audit trail</li>
            </ul>
        </div>

        <div class="back-link">
            <a href="/inventory">← Back to Inventory Module</a>
        </div>
    </div>
</body>

</html>