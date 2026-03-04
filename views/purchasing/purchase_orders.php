<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders - Purchasing Module</title>
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
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .feature-card {
            background: #313244;
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid #45475a;
        }
        .feature-card h3 {
            color: #94e2d5;
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }
        .feature-card p {
            margin: 0;
            font-size: 0.9rem;
            color: #bac2de;
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
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }
        .status-draft { background: #585b70; color: #cdd6f4; }
        .status-sent { background: #89b4fa; color: #1e1e2e; }
        .status-received { background: #a6e3a1; color: #1e1e2e; }
        .status-cancelled { background: #f38ba8; color: #1e1e2e; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Purchase Orders</h1>
        
        <div class="coming-soon">
            <h2>✨ Coming Soon</h2>
            <p>The purchase orders management page is under construction.</p>
        </div>
        
        <h3 style="color: #94e2d5;">Planned Features:</h3>
        <div class="features">
            <div class="feature-card">
                <h3>➕ Create PO</h3>
                <p>Create new purchase orders with line items from suppliers</p>
            </div>
            <div class="feature-card">
                <h3>📋 PO List</h3>
                <p>View and filter purchase orders by status, supplier, date</p>
            </div>
            <div class="feature-card">
                <h3>✉️ Send/Approve</h3>
                <p>Approval workflow for purchase orders</p>
            </div>
            <div class="feature-card">
                <h3>📬 Receiving</h3>
                <p>Receive items against POs with quantity verification</p>
            </div>
        </div>
        
        <h3 style="color: #94e2d5;">PO Status Flow:</h3>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin: 1rem 0;">
            <span class="status-badge status-draft">DRAFT</span> →
            <span class="status-badge status-sent">SENT</span> →
            <span class="status-badge status-received">RECEIVED</span>
            <span style="margin: 0 0.5rem;">or</span>
            <span class="status-badge status-cancelled">CANCELLED</span>
        </div>
        
        <div class="note">
            <strong>📝 Note:</strong> Database schema already includes:
            <ul style="margin: 0.5rem 0 0 1.5rem; color: #bac2de;">
                <li>po_number (unique)</li>
                <li>supplier_id with foreign key</li>
                <li>status with DEFAULT 'DRAFT'</li>
                <li>ordered_at, received_at timestamps</li>
                <li>purchase_order_lines with item_id, qty, cost</li>
            </ul>
        </div>
        
        <a href="/purchasing" class="back-link">← Back to Purchasing Module</a>
    </div>
</body>
</html>