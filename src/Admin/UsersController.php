<?php
namespace App\Admin;

use App\Core\View;

class UsersController
{
    public static function index()
    {
        $pdo = db();
        $stmt = $pdo->query("SELECT id, username, created_at FROM users ORDER BY id ASC");
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return View::render('admin/users', ['users' => $users]);
    }
}