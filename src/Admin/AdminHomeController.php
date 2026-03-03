<?php
namespace App\Admin;

use App\Core\View;

class AdminHomeController
{
    public static function index()
    {
        return View::render('admin/index');
    }
}