<h1>Users</h1>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= $user['id'] ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= $user['created_at'] ?></td>
        <td>
            <a href="<?= APP_BASE_PATH ?>/admin/user-roles?user_id=<?= $user['id'] ?>">Manage Roles</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<p><a href="<?= APP_BASE_PATH ?>/admin">Back to Admin</a></p>