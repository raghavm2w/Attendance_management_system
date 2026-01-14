<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class LeaveController extends BaseController
{
    public function index()
    {
        return view('user/leaves');
    }
}
