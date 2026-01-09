<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AttendanceController extends BaseController
{
    public function index()
    {
        return view('admin/attendance');
    }
}
