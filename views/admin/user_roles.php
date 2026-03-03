<h1>Manage Roles for <?= htmlspecialchars($user['username']) ?> (ID: <?= $user['id'] ?>)</h1>

<form method="POST" action="<?= APP_BASE_PATH ?>/admin/user-roles">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

    <?php foreach ($allRoles as $role): ?>
    <div>
        <label>
            <input type="checkbox" name="role_ids[]" value="<?= $role['id'] ?>"
                <?= in_array($role['id'], $assignedRoleIds) ? 'checked' : '' ?>>
            <?= htmlspecialchars($role['label'] ?: $role['name']) ?>
        </label>
    </div>
    <?php endforeach; ?>

    <button type="submit">Save Roles</button>
    <a href="<?= APP_BASE_PATH ?>/admin/users">Cancel</a>
</form>