<h1>POS Registers</h1>

<h2>Add New Register</h2>
<form method="POST" action="<?= APP_BASE_PATH ?>/admin/registers/create">
    <div>
        <label>Register Code *</label><br>
        <input type="text" name="register_code" required>
    </div>
    <div>
        <label>Machine Name</label><br>
        <input type="text" name="machine_name">
    </div>
    <div>
        <label>Serial No</label><br>
        <input type="text" name="serial_no">
    </div>
    <div>
        <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button type="submit">Add Register</button>
</form>

<h2>Existing Registers</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Machine Name</th>
        <th>Serial No</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($registers as $reg): ?>
    <tr>
        <td><?= $reg['id'] ?></td>
        <td><?= htmlspecialchars($reg['register_code']) ?></td>
        <td><?= htmlspecialchars($reg['machine_name']) ?></td>
        <td><?= htmlspecialchars($reg['serial_no']) ?></td>
        <td><?= $reg['is_active'] ? 'Active' : 'Inactive' ?></td>
        <td>
            <form method="POST" action="<?= APP_BASE_PATH ?>/admin/registers/toggle" style="display:inline;">
            <input type="hidden" name="register_id" value="<?= $reg['id'] ?>">
            <input type="hidden" name="is_active" value="<?= $reg['is_active'] ? 0 : 1 ?>">
            <button type="submit"><?= $reg['is_active'] ? 'Disable' : 'Enable' ?></button>
        </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<p><a href="<?= APP_BASE_PATH ?>/admin">Back to Admin</a></p>