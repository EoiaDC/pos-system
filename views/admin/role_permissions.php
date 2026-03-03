<h1>Permissions for Role: <?= htmlspecialchars($role['label'] ?: $role['name']) ?></h1>

<?php if (empty($permissions)): ?>
    <p>This role has no permissions assigned.</p>
<?php else: ?>
    <ul>
        <?php foreach ($permissions as $perm): ?>
        <li><?= htmlspecialchars($perm['code']) ?> — <?= htmlspecialchars($perm['label']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="<?= APP_BASE_PATH ?>/admin/roles">Back to Roles</a></p>