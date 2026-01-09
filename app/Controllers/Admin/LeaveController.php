<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LeaveController extends BaseController
{
    public function index()
    {
        return view('admin/leaves');
    }
}
