<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEV ONLY - Sample Item Helper</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dev-badge {
            background: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #721c24;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #218838;
        }

        .back-link {
            margin-top: 30px;
            display: block;
            color: #666;
            text-decoration: none;
        }

        .back-link:hover {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>
            <span>📝 Sample Item Helper</span>
            <span class="dev-badge">DEV ONLY</span>
        </h1>

        <div class="info-box">
            <strong>ℹ️ Purpose:</strong> This helper creates a sample item for sales testing.
            <br><br>
            <strong>Current database status:</strong>
            <table>
                <tr>
                    <th>Categories found:</th>
                    <td><?php echo isset($category) && $category ? '✅ Yes (ID: ' . $category['id'] . ' - ' . htmlspecialchars($category['name']) . ')' : '❌ None - create a category first'; ?></td>
                </tr>
                <tr>
                    <th>Units of Measure found:</th>
                    <td><?php echo isset($uom) && $uom ? '✅ Yes (ID: ' . $uom['id'] . ' - ' . htmlspecialchars($uom['code']) . ')' : '❌ None - create a UOM first'; ?></td>
                </tr>
            </table>
        </div>

        <?php if (isset($message)): ?>
            <div class="success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <button type="submit">➕ Insert Sample Item</button>
        </form>

        <a href="/inventory" class="back-link">← Back to Inventory Module</a>
    </div>
</body>

</html>