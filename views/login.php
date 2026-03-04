<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS System</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 3px; cursor: pointer; width: 100%; }
        button:hover { background: #0056b3; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 POS System Login</h1>
        
        <?php
        // Display flash messages
        if (class_exists('\\POS\\Core\\Response')) {
            $errorFlash = \POS\Core\Response::getFlash('error');
            if ($errorFlash): ?>
                <div class="error">
                    <?= htmlspecialchars($errorFlash) ?>
                </div>
            <?php endif;
        }
        ?>
        
        <form method="POST" action="/pos-system/public/login">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        
        <p style="margin-top: 20px; text-align: center; color: #6c757d;">
            Default: admin / admin123
        </p>
    </div>
</body>
</html>