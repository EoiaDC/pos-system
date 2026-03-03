<h1>Roles</h1>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Label</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($roles as $role): ?>
    <tr>
        <td><?= $role['id'] ?></td>
        <td><?= htmlspecialchars($role['name']) ?></td>
        <td><?= htmlspecialchars($role['label']) ?></td>
        <td><?= $role['created_at'] ?></td>
        <td>
            <a href="<?= APP_BASE_PATH ?>/admin/role-permissions?role_id=<?= $role['id'] ?>">View Permissions</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<p><a href="<?= APP_BASE_PATH ?>/admin">Back to Admin</a></p>