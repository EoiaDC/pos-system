<?php
namespace App\Admin;

use App\Core\View;
use App\Core\BirReadiness;

class BirReadinessController
{
    public static function index()
    {
        $status = BirReadiness::status();
        return View::render('admin/bir_readiness', ['status' => $status]);
    }
}