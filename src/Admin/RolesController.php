<?php
namespace App\Admin;

use App\Core\View;

class RolesController
{
    public static function index()
    {
        $pdo = db();
        $stmt = $pdo->query("SELECT id, name, label, created_at FROM roles ORDER BY name");
        $roles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return View::render('admin/roles', ['roles' => $roles]);
    }
}