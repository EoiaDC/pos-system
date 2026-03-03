<h1>OR Series</h1>

<h2>Add New OR Series</h2>
<form method="POST" action="<?= APP_BASE_PATH ?>/admin/or-series/create">
    <div>
        <label>Series Code *</label><br>
        <input type="text" name="series_code" required>
    </div>
    <div>
        <label>Start Number *</label><br>
        <input type="number" name="start_no" required min="1">
    </div>
    <div>
        <label>End Number *</label><br>
        <input type="number" name="end_no" required min="1">
    </div>
    <?php if (!empty($registers)): ?>
    <div>
        <label>Register</label><br>
        <select name="register_id">
            <option value="">-- None --</option>
            <?php foreach ($registers as $reg): ?>
            <option value="<?= $reg['id'] ?>"><?= htmlspecialchars($reg['register_code']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    <div>
        <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button type="submit">Create Series</button>
</form>

<h2>Existing OR Series</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Start</th>
        <th>End</th>
        <th>Current</th>
        <th>Register</th>
        <th>Status</th>
    </tr>
    <?php foreach ($series as $s): ?>
    <tr>
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['series_code']) ?></td>
        <td><?= $s['start_no'] ?></td>
        <td><?= $s['end_no'] ?></td>
        <td><?= $s['current_no'] ?></td>
        <td><?= $s['register_id'] ?? '—' ?></td>
        <td><?= $s['is_active'] ? 'Active' : 'Inactive' ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<p><a href="<?= APP_BASE_PATH ?>/admin">Back to Admin</a></p>