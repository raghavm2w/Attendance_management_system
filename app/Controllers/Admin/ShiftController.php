<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ShiftController extends BaseController
{
    public function index()
    {
        return view('admin/shifts');
    }
}
