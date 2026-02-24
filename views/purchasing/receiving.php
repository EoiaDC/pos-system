<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receiving - Purchasing Module</title>
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
        .workflow {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 2rem 0;
        }
        .workflow-step {
            background: #313244;
            padding: 1rem;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #45475a;
        }
        .workflow-step .step-number {
            display: inline-block;
            width: 24px;
            height: 24px;
            background: #89b4fa;
            color: #1e1e2e;
            border-radius: 50%;
            line-height: 24px;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .workflow-step h3 {
            color: #94e2d5;
            margin: 0.5rem 0;
            font-size: 1rem;
        }
        .workflow-step p {
            margin: 0;
            font-size: 0.9rem;
            color: #bac2de;
        }
        .features {
            background: #313244;
            padding: 1.5rem;
            border-radius: 6px;
            margin: 2rem 0;
            border: 1px solid #45475a;
        }
        .features h3 {
            color: #94e2d5;
            margin-top: 0;
        }
        .features ul {
            list-style: none;
            padding: 0;
        }
        .features li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #45475a;
        }
        .features li:last-child {
            border-bottom: none;
        }
        .features li:before {
            content: "📦 ";
            margin-right: 0.5rem;
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
        <h1>📬 Receiving</h1>
        
        <div class="coming-soon">
            <h2>✨ Coming Soon</h2>
            <p>The receiving module is under construction and will integrate with inventory.</p>
        </div>
        
        <h3 style="color: #94e2d5;">Receiving Workflow:</h3>
        <div class="workflow">
            <div class="workflow-step">
                <div class="step-number">1</div>
                <h3>Select PO</h3>
                <p>Choose purchase order to receive against</p>
            </div>
            <div class="workflow-step">
                <div class="step-number">2</div>
                <h3>Input Quantities</h3>
                <p>Enter delivered quantities for each line item</p>
            </div>
            <div class="workflow-step">
                <div class="step-number">3</div>
                <h3>Post to Stock</h3>
                <p>Update inventory with received items</p>
            </div>
        </div>
        
        <div class="features">
            <h3>📋 Planned Features:</h3>
            <ul>
                <li>Select PO from list of SENT/APPROVED purchase orders</li>
                <li>Input delivered quantities with partial receiving support</li>
                <li>Auto-calculate received vs ordered quantities</li>
                <li>Post to stock movement with lot/serial tracking</li>
                <li>Generate receiving reports</li>
                <li>Print receiving slips</li>
            </ul>
        </div>
        
        <div class="note">
            <strong>📝 Note:</strong> Receiving will update inventory stock levels and 
            mark POs as RECEIVED when complete. Integration with stock movement 
            and inventory valuation will be implemented in a future phase.
        </div>
        
        <a href="/purchasing" class="back-link">← Back to Purchasing Module</a>
    </div>
</body>
</html>