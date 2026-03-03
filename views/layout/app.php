<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Display flash messages if Response class exists
        if (class_exists('\\POS\\Core\\Response')) {
            $errorFlash = \POS\Core\Response::getFlash('error');
            $successFlash = \POS\Core\Response::getFlash('success');
            
            if ($errorFlash): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px;">
                    <strong>Error:</strong> <?= htmlspecialchars($errorFlash) ?>
                </div>
            <?php endif;
            
            if ($successFlash): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 4px;">
                    <strong>Success:</strong> <?= htmlspecialchars($successFlash) ?>
                </div>
            <?php endif;
        }
        ?>
        
        <!-- Main content injected by router -->
<?php echo $content ?? '<!-- content will appear here -->'; ?>
    </div>
</body>
</html>