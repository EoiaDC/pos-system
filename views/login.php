<h1>Login</h1>

<?php $error = flash('error'); ?>
<?php if ($error): ?>
    <div style="color: red; border: 1px solid red; padding: 8px; margin-bottom: 12px;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= APP_BASE_PATH ?>/login">
    <div>
        <label>Username</label><br>
        <input type="text" name="username" required>
    </div>
    <div style="margin-top:8px;">
        <label>Password</label><br>
        <input type="password" name="password" required>
    </div>
    <div style="margin-top:12px;">
        <button type="submit">Login</button>
    </div>
</form>
