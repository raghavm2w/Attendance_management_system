<?php

namespace App\Controllers\Cron;

use App\Controllers\BaseController;
use App\Services\AttendanceService;

class AttendanceCron extends BaseController
{
    public function markDailyAttendance()
    {
        $attendanceService = new AttendanceService();
        $attendanceService->processDailyAttendance();

        echo "Attendance cron executed successfully";
    }
}
