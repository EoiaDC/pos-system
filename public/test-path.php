<?php
echo "<h1>Path Test</h1>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "</pre>";
echo "<p>Try these links:</p>";
echo "<ul>";
echo "<li><a href='/pos-system/public/'>/pos-system/public/</a></li>";
echo "<li><a href='/pos-system/public/index.php'>/pos-system/public/index.php</a></li>";
echo "<li><a href='/pos-system/public/test-path.php'>/pos-system/public/test-path.php</a></li>";
echo "<li><a href='/pos-system/public/sales'>/pos-system/public/sales</a></li>";
echo "</ul>";