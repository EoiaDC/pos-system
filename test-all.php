<?php
require __DIR__ . '/config/bootstrap.php';

// Ensure Core classes are loaded
require_once __DIR__ . '/src/Core/Request.php';
require_once __DIR__ . '/src/Core/Response.php';
require_once __DIR__ . '/src/Core/Validator.php';

use POS\Core\{Request, Response, Validator};

// Set a test flash
Response::flash('test', 'Integration test running...');

?>
<!DOCTYPE html>
<html>
<head>
    <title>POS System Integration Test</title>
    <style>
        body { font-family: 'Segoe UI', monospace; padding: 20px; background: #1e1e2f; color: #fff; }
        .container { max-width: 1000px; margin: 0 auto; background: #2d2d44; padding: 20px; border-radius: 10px; }
        h1 { color: #a5d6ff; border-bottom: 2px solid #4a4a6a; padding-bottom: 10px; }
        h2 { color: #ffd966; margin-top: 30px; }
        .pass { color: #9bff9b; font-weight: bold; }
        .fail { color: #ff9b9b; font-weight: bold; }
        .warning { color: #ffd966; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #4a4a6a; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #4a4a6a; }
        .summary { background: #1a1a2e; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #aaa; text-align: center; }
        .flash-test { 
            background: #2d2d44; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #ffd966;
            margin: 20px 0;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .badge.dev-a { background: #4a4a6a; color: #a5d6ff; }
        .badge.dev-b { background: #4a4a6a; color: #9bff9b; }
        .badge.dev-c { background: #4a4a6a; color: #ffd966; }
        .badge.dev-d { background: #4a4a6a; color: #ff9b9b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 POS SYSTEM — COMPLETE INTEGRATION TEST</h1>
        <p>Lead: DEV A | Date: <?= date('Y-m-d H:i:s') ?></p>
        
        <div class="flash-test">
            <strong>💬 Flash Test:</strong> 
            <?php
            $testFlash = Response::getFlash('test');
            if ($testFlash) {
                echo "<span class='pass'>✅ " . htmlspecialchars($testFlash) . "</span>";
            } else {
                echo "<span class='fail'>❌ Flash not working</span>";
            }
            ?>
        </div>
        
        <?php
        // TEST 1: DEV A Helpers
        echo "<h2>📋 <span class='badge dev-a'>DEV A</span> Core Helpers</h2>";
        echo "<table>";
        echo "<tr><th>Test</th><th>Result</th></tr>";
        
        try {
            echo "<tr><td>Request::method()</td><td class='pass'>" . Request::method() . "</td></tr>";
            echo "<tr><td>Request::path()</td><td class='pass'>" . Request::path() . "</td></tr>";
            echo "<tr><td>Request::ip()</td><td class='pass'>" . Request::ip() . "</td></tr>";
            
            // Test Validator
            $test = Validator::validate(['name' => ''], ['name' => ['required']]);
            $validTest = !$test['ok'] ? "<span class='pass'>✅ Working</span>" : "<span class='fail'>❌ Failed</span>";
            echo "<tr><td>Validator (required check)</td><td>$validTest</td></tr>";
            
            echo "<tr><td>Response class</td><td class='pass'>✅ Loaded</td></tr>";
            
        } catch (Exception $e) {
            echo "<tr><td colspan='2' class='fail'>❌ Error: " . $e->getMessage() . "</td></tr>";
        }
        echo "</table>";
        
        // TEST 2: Database
        echo "<h2>🗄️ Database Connection</h2>";
        echo "<table>";
        
        try {
            $db = require __DIR__ . '/config/database.php';
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}",
                $db['user'],
                $db['pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<tr><td>Database</td><td class='pass'>✅ {$db['dbname']}</td></tr>";
            
            // Get all tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "<tr><td>Total tables</td><td class='pass'>✅ " . count($tables) . " found</td></tr>";
            
            // DEV B — Units of Measure
            echo "<tr><th colspan='2' style='background:#3d3d5a;'><span class='badge dev-b'>DEV B</span> Units of Measure</th></tr>";
            
            if (in_array('units_of_measure', $tables)) {
                $count = $pdo->query("SELECT COUNT(*) FROM units_of_measure")->fetchColumn();
                echo "<tr><td>units_of_measure</td><td class='pass'>✅ EXISTS ($count records)</td></tr>";
            } else {
                echo "<tr><td>units_of_measure</td><td class='fail'>❌ MISSING</td></tr>";
            }
            
            // DEV C — Purchase Orders
            echo "<tr><th colspan='2' style='background:#3d3d5a;'><span class='badge dev-c'>DEV C</span> Purchase Orders</th></tr>";
            
            if (in_array('purchase_orders', $tables)) {
                echo "<tr><td>purchase_orders</td><td class='pass'>✅ EXISTS</td></tr>";
            } else {
                echo "<tr><td>purchase_orders</td><td class='fail'>❌ MISSING</td></tr>";
            }
            
            if (in_array('purchase_order_items', $tables)) {
                echo "<tr><td>purchase_order_items</td><td class='pass'>✅ EXISTS</td></tr>";
            } else {
                echo "<tr><td>purchase_order_items</td><td class='fail'>❌ MISSING</td></tr>";
            }
            
            // DEV D — Audit Logs
            echo "<tr><th colspan='2' style='background:#3d3d5a;'><span class='badge dev-d'>DEV D</span> Audit Logs</th></tr>";
            
            if (in_array('audit_logs', $tables)) {
                echo "<tr><td>audit_logs</td><td class='pass'>✅ EXISTS</td></tr>";
            } else {
                echo "<tr><td>audit_logs</td><td class='fail'>❌ MISSING</td></tr>";
            }
            
        } catch (PDOException $e) {
            echo "<tr><td colspan='2' class='fail'>❌ Connection failed: " . $e->getMessage() . "</td></tr>";
        }
        echo "</table>";
        
        // Summary
        echo "<div class='summary'>";
        echo "<h3>📊 Integration Summary</h3>";
        
        $allGood = true;
        
        if (!in_array('units_of_measure', $tables ?? [])) $allGood = false;
        if (!in_array('purchase_orders', $tables ?? [])) $allGood = false;
        if (!in_array('purchase_order_items', $tables ?? [])) $allGood = false;
        if (!in_array('audit_logs', $tables ?? [])) $allGood = false;
        
        if ($allGood) {
            echo "<p class='pass'>✅✅ ALL SYSTEMS OPERATIONAL — DEV A helpers ready for use!</p>";
        } else {
            echo "<p class='warning'>⚠️ Some tables missing — run <a href='migrate.php' style='color:#a5d6ff;'>migrations</a> first.</p>";
        }
        ?>
        
        </div>
        
        <div class="footer">
            <p>✅ Test complete | <a href="migrate.php" style="color: #a5d6ff;">Run Migrations</a></p>
        </div>
    </div>
</body>
</html>