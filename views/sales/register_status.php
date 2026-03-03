<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Status - POS System</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .status-card { background: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 3px; }
        .status-item { margin: 15px 0; font-size: 1.1em; }
        .status-label { font-weight: bold; color: #555; display: inline-block; width: 150px; }
        .status-placeholder { color: #999; font-style: italic; }
        .note { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 3px; margin: 20px 0; }
        .back-link { display: inline-block; margin-top: 20px; padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 3px; }
        .back-link:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🪙 Register Status</h1>
        <div class="bir-safety-note" style="background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 3px;">
            <strong>🛡️ BIR Safety Gate Notice:</strong>
            <p style="margin: 10px 0 0 0;">
                Sales transactions will be blocked unless:
            </p>
            <ul style="margin-top: 5px; margin-bottom: 5px;">
                <li>✅ Company Profile is completed</li>
                <li>✅ At least one Active POS Register exists</li>
                <li>✅ At least one Active OR Series is configured</li>
            </ul>
            <p style="margin: 10px 0 0 0;">
                <a href="/admin/bir-readiness" style="color: #007bff; text-decoration: underline;">View BIR Readiness Checklist →</a>
            </p>
        </div>        
        <div class="status-card">
            <div class="status-item">
                <span class="status-label">Active Register:</span>
                <span class="status-placeholder">(coming from pos_registers table)</span>
            </div>
            <div class="status-item">
                <span class="status-label">Current OR Series:</span>
                <span class="status-placeholder">(coming from or_series table)</span>
            </div>
            <div class="status-item">
                <span class="status-label">Next OR Number:</span>
                <span class="status-placeholder">(automatically incrementing)</span>
            </div>
        </div>
        
        <div class="note">
            <strong>📌 BIR Compliance Note:</strong>
            <p>Status checks will be enforced in the Sales engine later:</p>
            <ul>
                <li>Cannot start sale without active register</li>
                <li>OR numbers automatically assigned per transaction</li>
                <li>All receipts tracked for audit purposes</li>
                <li>Register must be opened before first sale</li>
            </ul>
        </div>
        
        <a href="/sales" class="back-link">← Back to Sales Module</a>
    </div>
</body>
</html>