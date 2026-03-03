<h1>Company Profile</h1>

<form method="POST" action="<?= APP_BASE_PATH ?>/admin/company-profile">
    <div>
        <label>Registered Name *</label><br>
        <input type="text" name="registered_name" value="<?= htmlspecialchars($profile['registered_name'] ?? '') ?>" required>
    </div>
    <div>
        <label>Trade Name</label><br>
        <input type="text" name="trade_name" value="<?= htmlspecialchars($profile['trade_name'] ?? '') ?>">
    </div>
    <div>
        <label>TIN *</label><br>
        <input type="text" name="tin" value="<?= htmlspecialchars($profile['tin'] ?? '') ?>" required>
    </div>
    <div>
        <label>Address</label><br>
        <textarea name="address"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
    </div>
    <!-- If you have vat_type column, add a select here -->
    <div>
        <button type="submit">Save</button>
        <a href="<?= APP_BASE_PATH ?>/admin">Cancel</a>
    </div>
</form>