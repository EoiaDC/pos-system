<?php
require __DIR__ . '/../config/bootstrap.php';

echo "<h1>BIR Readiness Debug</h1>";

// Check if BirReadiness class exists
if (class_exists('App\Core\BirReadiness')) {
    echo "<p>✅ BirReadiness class found</p>";
    
    $status = App\Core\BirReadiness::getReadinessStatus();
    
    echo "<h2>Status:</h2>";
    echo "<pre>";
    print_r($status);
    echo "</pre>";
    
    echo "<h2>Missing Requirements:</h2>";
    echo "<pre>";
    print_r(App\Core\BirReadiness::getMissingRequirements());
    echo "</pre>";
} else {
    echo "<p>❌ BirReadiness class not found</p>";
}