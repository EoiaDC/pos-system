<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Module</title>
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

        .placeholder {
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
            margin: 5px 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        li a {
            text-decoration: none;
            color: #0066cc;
            display: block;
            font-weight: bold;
        }

        li a:hover {
            color: #004499;
            text-decoration: underline;
        }

        .note {
            color: #666;
            font-style: italic;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📦 Inventory Module</h1>

        <div class="placeholder">
            <h2>Management Pages</h2>
            <ul>
                <li><a href="/inventory/items">📝 Items</a></li>
                <li><a href="/inventory/categories">📁 Categories</a></li>
                <li><a href="/inventory/uom">⚖️ Units of Measure</a></li>
            </ul>
        </div>

        <p class="note">Note: Stock tracking will be implemented later.</p>
    </div>
</body>

</html>