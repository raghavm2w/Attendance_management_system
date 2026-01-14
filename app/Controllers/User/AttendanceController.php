<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class AttendanceController extends BaseController
{
    public function index()
    {
        return view('user/attendance');
    }
}
