<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draft Sale - POS System</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .error-box { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 3px; }
        .sale-card { background: #f8f9fa; border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .sale-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .sale-id { font-size: 1.5em; font-weight: bold; color: #007bff; }
        .status-badge { background: #ffc107; color: #333; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin: 20px 0; }
        .info-item { padding: 10px; background: white; border-radius: 3px; }
        .info-label { font-weight: bold; color: #555; font-size: 0.9em; }
        .info-value { font-size: 1.1em; margin-top: 5px; }
        .bir-section { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 3px; }
        .bir-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
        .bir-item { padding: 8px; background: rgba(255,255,255,0.5); border-radius: 3px; }
        .bir-ok { color: #28a745; font-weight: bold; }
        .bir-not-ok { color: #dc3545; font-weight: bold; }
        .overall-badge { padding: 8px; border-radius: 3px; text-align: center; font-weight: bold; margin-top: 10px; }
        .overall-ready { background: #d4edda; color: #155724; }
        .overall-not-ready { background: #f8d7da; color: #721c24; }
        .placeholder-note { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; margin: 20px 0; border-radius: 3px; }
        .btn-secondary { background: #6c757d; color: white; text-decoration: none; display: inline-block; padding: 8px 15px; border-radius: 3px; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-warning { background: #ffc107; color: #333; text-decoration: none; display: inline-block; padding: 8px 15px; border-radius: 3px; margin-left: 10px; }
        .btn-warning:hover { background: #e0a800; }
        .selection-forms { display: flex; gap: 20px; margin: 20px 0; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; margin-bottom: 10px; }
        .btn-update { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; width: 100%; }
        .btn-update:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 Draft Sale</h1>
        
        <?php
        // Display flash messages if any
        if (class_exists('\\POS\\Core\\Response')) {
            $successFlash = \POS\Core\Response::getFlash('success');
            if ($successFlash): ?>
                <div class="success-box" style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 3px;">
                    <strong>✅ Success:</strong> <?= htmlspecialchars($successFlash) ?>
                </div>
            <?php endif;
        }
        ?>
        
        <?php if (isset($error)): ?>
            <div class="error-box">
                <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
            <a href="/sales" class="btn-secondary">← Back to Sales Module</a>
        
        <?php elseif (!$sale): ?>
            <div class="error-box">
                <strong>❌ Sale Not Found</strong>
                <p>The requested sale could not be found in the database.</p>
            </div>
            <a href="/sales" class="btn-secondary">← Back to Sales Module</a>
        
        <?php else: ?>
            <div class="sale-header">
                <span class="sale-id">Sale #<?= htmlspecialchars($sale['id']) ?></span>
                <span class="status-badge"><?= htmlspecialchars($sale['status']) ?></span>
            </div>
            
            <div class="sale-card">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Created By (ID)</div>
                        <div class="info-value"><?= htmlspecialchars($sale['created_by'] ?? 'Unknown') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Created At</div>
                        <div class="info-value"><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($sale['created_at']))) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">POS Register ID</div>
                        <div class="info-value"><?= $sale['pos_register_id'] ? htmlspecialchars($sale['pos_register_id']) : '<em>(not selected)</em>' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">OR Series ID</div>
                        <div class="info-value"><?= $sale['or_series_id'] ? htmlspecialchars($sale['or_series_id']) : '<em>(not selected)</em>' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">OR Number</div>
                        <div class="info-value"><?= $sale['or_no'] ? htmlspecialchars($sale['or_no']) : '<em>(not reserved)</em>' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Line Items</div>
                        <div class="info-value">(not yet implemented)</div>
                    </div>
                </div>
            </div>

            <!-- NEW: Register and OR Series Selection Forms -->
            <?php if ($sale['status'] === 'DRAFT'): ?>
            <div class="selection-forms">
                <!-- Register Selection Form -->
                <div class="form-group">
                    <form method="POST" action="/sales/register/update">
                        <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                        <label for="register_id">Select POS Register:</label>
                        <select name="register_id" id="register_id">
                            <option value="">-- Select Register --</option>
                            <?php
                            // Get active registers from database
                            $config = require __DIR__ . '/../../config/database.php';
                            $db = new \PDO(
                                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                                $config['user'],
                                $config['pass']
                            );
                            $stmt = $db->query("SELECT id, register_code, register_name FROM pos_registers WHERE is_active = 1 ORDER BY register_code");
                            $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                            foreach ($registers as $register):
                                $selected = ($register['id'] == $sale['pos_register_id']) ? 'selected' : '';
                            ?>
                                <option value="<?= $register['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($register['register_code']) ?> - <?= htmlspecialchars($register['register_name'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-update">Update Register</button>
                    </form>
                </div>
                
                <!-- OR Series Selection Form -->
                <div class="form-group">
                    <form method="POST" action="/sales/or-series/update">
                        <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                        <label for="or_series_id">Select OR Series:</label>
                        <select name="or_series_id" id="or_series_id">
                            <option value="">-- Select OR Series --</option>
                            <?php
                            // Get active OR series from database
                            $stmt = $db->query("SELECT id, series_code, start_no, end_no, current_no FROM or_series WHERE is_active = 1 ORDER BY series_code");
                            $series = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                            foreach ($series as $orSeries):
                                $selected = ($orSeries['id'] == $sale['or_series_id']) ? 'selected' : '';
                            ?>
                                <option value="<?= $orSeries['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($orSeries['series_code']) ?> 
                                    (<?= $orSeries['start_no'] ?> - <?= $orSeries['end_no'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-update">Update OR Series</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="bir-section">
                <strong>🛡️ BIR Readiness Snapshot (read-only)</strong>
                <div class="bir-grid">
                    <div class="bir-item">
                        <span class="info-label">Company Profile:</span>
                        <span class="<?= $birStatus['company_profile_ok'] ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= $birStatus['company_profile_ok'] ? '✅ OK' : '❌ NOT OK' ?>
                        </span>
                    </div>
                    <div class="bir-item">
                        <span class="info-label">Active Register:</span>
                        <span class="<?= $birStatus['has_active_register'] ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= $birStatus['has_active_register'] ? '✅ OK' : '❌ NOT OK' ?>
                        </span>
                    </div>
                    <div class="bir-item">
                        <span class="info-label">Active OR Series:</span>
                        <span class="<?= $birStatus['has_active_or_series'] ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= $birStatus['has_active_or_series'] ? '✅ OK' : '❌ NOT OK' ?>
                        </span>
                    </div>
                    <div class="bir-item">
                        <span class="info-label">Overall Status:</span>
                        <span class="<?= $birStatus['overall_ok'] ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= $birStatus['overall_ok'] ? '✅ READY' : '❌ NOT READY' ?>
                        </span>
                    </div>
                </div>
                
                <?php if (!$birStatus['overall_ok']): ?>
                    <div style="margin-top: 15px;">
                        <a href="/admin/bir-readiness" class="btn-warning">🔧 Fix BIR Readiness</a>
                    </div>
                <?php endif; ?>
            </div>
                        <!-- Audit Trail Section -->
            <div style="background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <strong>📋 Audit Trail</strong>
                <?php
                // Load audit logs for this sale
                try {
                    $stmt = $db->prepare("
                        SELECT * FROM audit_logs 
                        WHERE table_name = 'sales_headers' AND record_id = ?
                        ORDER BY created_at DESC
                        LIMIT 10
                    ");
                    $stmt->execute([$sale['id']]);
                    $auditLogs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    
                    if (empty($auditLogs)): ?>
                        <p style="color: #6c757d; margin-top: 10px;">No audit records found for this sale.</p>
                    <?php else: ?>
                        <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #e9ecef;">
                                    <th style="padding: 8px; text-align: left;">Time</th>
                                    <th style="padding: 8px; text-align: left;">User</th>
                                    <th style="padding: 8px; text-align: left;">Action</th>
                                    <th style="padding: 8px; text-align: left;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($auditLogs as $log): ?>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 8px;"><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($log['created_at']))) ?></td>
                                    <td style="padding: 8px;"><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                                    <td style="padding: 8px;">
                                        <?php
                                        $action = $log['action'];
                                        $badgeColor = match($action) {
                                            'sale.started' => '#28a745',
                                            'sale.register.selected' => '#007bff',
                                            'sale.or_series.selected' => '#007bff',
                                            default => '#6c757d'
                                        };
                                        ?>
                                        <span style="background: <?= $badgeColor ?>; color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.85em;">
                                            <?= htmlspecialchars($action) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 8px;">
                                        <?php
                                        $data = json_decode($log['new_data'], true);
                                        if ($action === 'sale.register.selected' && isset($data['register_code'])) {
                                            echo "Register: " . htmlspecialchars($data['register_code']);
                                        } elseif ($action === 'sale.or_series.selected' && isset($data['series_code'])) {
                                            echo "OR Series: " . htmlspecialchars($data['series_code']);
                                        } elseif ($action === 'sale.started') {
                                            echo "Draft created";
                                        } else {
                                            echo "—";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif;
                } catch (\Exception $e) {
                    echo "<p style='color: #dc3545;'>Error loading audit trail.</p>";
                }
                ?>
            </div>
            
                        <!-- Line Items Section -->
            <div style="background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <strong>🛒 Line Items</strong>
                
                <?php
                // Load lines for this sale
                try {
                    $stmt = $db->prepare("
                        SELECT * FROM sales_lines 
                        WHERE sale_id = ?
                        ORDER BY id ASC
                    ");
                    $stmt->execute([$sale['id']]);
                    $lines = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    
                    if (empty($lines)): ?>
                        <p style="color: #6c757d; margin: 15px 0;">No items added yet.</p>
                    <?php else: ?>
                        <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #e9ecef;">
                                    <th style="padding: 8px; text-align: left;">Item</th>
                                    <th style="padding: 8px; text-align: right;">Qty</th>
                                    <th style="padding: 8px; text-align: right;">Price</th>
                                    <th style="padding: 8px; text-align: right;">Discount</th>
                                    <th style="padding: 8px; text-align: right;">Total</th>
                                    <th style="padding: 8px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lines as $line): ?>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 8px;"><?= htmlspecialchars($line['description']) ?></td>
                                    <td style="padding: 8px; text-align: right;"><?= number_format($line['qty'], 2) ?></td>
                                    <td style="padding: 8px; text-align: right;">₱<?= number_format($line['unit_price'], 2) ?></td>
                                    <td style="padding: 8px; text-align: right;">₱<?= number_format($line['line_discount'] ?? 0, 2) ?></td>
                                    <td style="padding: 8px; text-align: right;">₱<?= number_format($line['line_total'], 2) ?></td>
                                    <td style="padding: 8px; text-align: center;">
                                        <form method="POST" action="/sales/line/remove" style="display: inline;">
                                            <input type="hidden" name="line_id" value="<?= $line['id'] ?>">
                                            <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                            <button type="submit" style="background: #dc3545; color: white; border: none; padding: 3px 8px; border-radius: 3px; cursor: pointer; font-size: 0.9em;">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: bold; background: #f8f9fa;">
                                    <td colspan="3" style="padding: 8px; text-align: right;">Subtotal:</td>
                                    <td style="padding: 8px; text-align: right;">₱<?= number_format($sale['subtotal'] ?? 0, 2) ?></td>
                                    <td></td>
                                </tr>
                                <tr style="font-weight: bold; background: #f8f9fa;">
                                    <td colspan="3" style="padding: 8px; text-align: right;">Total:</td>
                                    <td style="padding: 8px; text-align: right;">₱<?= number_format($sale['grand_total'] ?? 0, 2) ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php endif;
                } catch (\Exception $e) {
                    echo "<p style='color: #dc3545;'>Error loading line items.</p>";
                }
                ?>
                            <!-- Post Sale Section -->
            <?php if ($sale['status'] === 'DRAFT'): ?>
            <div style="background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 3px;">
                <strong>📋 Pre-Posting Checklist</strong>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px;">
                    <div>
                        <span class="info-label">BIR Readiness:</span>
                        <span class="<?= $birStatus['overall_ok'] ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= $birStatus['overall_ok'] ? '✅ OK' : '❌ NOT OK' ?>
                        </span>
                    </div>
                    <div>
                        <span class="info-label">Line Items:</span>
                        <span class="<?= ($lineCount ?? 0) > 0 ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= ($lineCount ?? 0) > 0 ? '✅ ' . $lineCount . ' items' : '❌ No items' ?>
                        </span>
                    </div>
                    <div>
                        <span class="info-label">Register Selected:</span>
                        <span class="<?= $sale['pos_register_id'] ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= $sale['pos_register_id'] ? '✅ Yes' : '❌ No' ?>
                        </span>
                    </div>
                    <div>
                        <span class="info-label">OR Series Selected:</span>
                        <span class="<?= $sale['or_series_id'] ? 'bir-ok' : 'bir-not-ok' ?>">
                            <?= $sale['or_series_id'] ? '✅ Yes' : '❌ No' ?>
                        </span>
                    </div>
                    <div>
                        <span class="info-label">Grand Total:</span>
                        <span class="bir-ok">₱<?= number_format($sale['grand_total'] ?? 0, 2) ?></span>
                    </div>
                </div>
                
                <form method="POST" action="/sales/draft/post" style="margin-top: 15px;">
                    <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                    <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 3px; cursor: pointer; font-weight: bold; width: 100%;">
                        ✅ Post Sale (Lock & Complete)
                    </button>
                </form>
                <p style="color: #6c757d; font-size: 0.9em; margin-top: 10px;">
                    <em>Posting locks this sale from further edits. No OR will be issued yet.</em>
                </p>
            </div>
            <?php else: ?>
            <!-- Sale is locked message -->
            <div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 3px;">
                <strong>🔒 Sale Locked</strong>
                <p style="margin-top: 10px;">This sale is <strong><?= htmlspecialchars($sale['status']) ?></strong> and cannot be modified.</p>
            </div>
            <?php endif; ?>
                            <!-- OR Issuance Section -->
            <?php if ($sale['status'] === 'POSTED'): ?>
                <?php if (empty($sale['or_no'])): ?>
                <div style="background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 3px;">
                    <strong>🧾 OR Issuance</strong>
                    
                    <?php if (empty($sale['or_series_id'])): ?>
                        <p style="color: #dc3545; margin-top: 10px;">
                            ⚠️ No OR series selected. Please select an OR series first.
                        </p>
                    <?php else: ?>
                        <p style="margin: 10px 0;">
                            <span class="info-label">Selected OR Series:</span>
                            <span class="bir-ok"><?= htmlspecialchars($sale['or_series_id']) ?></span>
                        </p>
                        
                        <form method="POST" action="/sales/or/issue" style="margin-top: 15px;">
                            <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                            <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 3px; cursor: pointer; font-weight: bold; width: 100%;">
                                🧾 Issue OR Number
                            </button>
                        </form>
                        <p style="color: #6c757d; font-size: 0.9em; margin-top: 10px;">
                            <em>This will reserve the next OR number from the selected series.</em>
                        </p>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 3px;">
                    <strong>🧾 OR Issued</strong>
                    <p style="margin-top: 10px; font-size: 1.2em;">
                        OR Number: <strong><?= htmlspecialchars($sale['or_no']) ?></strong>
                    </p>
                </div>
                <?php endif; ?>
            <?php elseif ($sale['status'] === 'DRAFT'): ?>
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 3px;">
                    <strong>🧾 OR Issuance</strong>
                    <p style="margin-top: 10px;">Post the sale first before issuing an OR number.</p>
                </div>
            <?php endif; ?>
                <!-- Add Item Form with Dropdown and Discount -->
                <?php if ($sale['status'] === 'DRAFT'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px;">
                    <strong>➕ Add Item</strong>
                    <form method="POST" action="/sales/line/add" style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
                        <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                        
                        <select name="item_id" required style="flex: 3; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                            <option value="">-- Select Item --</option>
                            <?php foreach ($itemsList as $item): ?>
                                <option value="<?= $item['id'] ?>" 
                                        data-price="<?= $item['selling_price'] ?>"
                                        data-name="<?= htmlspecialchars($item['item_name']) ?>">
                                    <?= htmlspecialchars($item['item_code'] ?? '') ?> - 
                                    <?= htmlspecialchars($item['item_name']) ?> 
                                    (ID:<?= $item['id'] ?>) 
                                    ₱<?= number_format($item['selling_price'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <input type="number" name="qty" placeholder="Qty" value="1" 
                               min="0.01" step="0.01" required 
                               style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                        
                        <input type="number" name="line_discount" placeholder="Discount" value="0.00" 
                               min="0" step="0.01" 
                               style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                        
                        <button type="submit" style="flex: 1; padding: 8px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">
                            Add Item
                        </button>
                    </form>
                    
                    <!-- Validation hint -->
                    <p style="color: #6c757d; font-size: 0.9em; margin-top: 10px;">
                        <em>Note: Discount cannot exceed item subtotal.</em>
                    </p>
                    
                    <!-- Price preview -->
                    <div id="selected-item-price" style="font-size: 0.9em; color: #6c757d;">
                        Selected item price will appear here
                    </div>
                    
                    <script>
                    // Simple price preview when item selected
                    document.querySelector('select[name="item_id"]').addEventListener('change', function(e) {
                        const selected = e.target.options[e.target.selectedIndex];
                        const priceDiv = document.getElementById('selected-item-price');
                        if (selected.value) {
                            const price = selected.dataset.price;
                            const name = selected.dataset.name;
                            priceDiv.innerHTML = `Selected: ${name} — Price: ₱${parseFloat(price).toFixed(2)}`;
                        } else {
                            priceDiv.innerHTML = 'Selected item price will appear here';
                        }
                    });
                    </script>
                </div>
                <?php endif; ?>

            <!-- Coming Soon Section (EXISTING) -->
            <div class="placeholder-note">
                <strong>📌 Coming Soon (Day 3):</strong>
                <ul>
                    <li>Line item entry</li>
                    <li>Cart interface</li>
                    <li>Totals calculation</li>
                    <li>OR issuance</li>
                </ul>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="/sales" class="btn-secondary">← Back to Sales Module</a>
        </div>
    </div>
</body>
</html>