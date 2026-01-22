<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\ShiftModel;
use App\Models\HolidayModel;

class AttendanceService
{
    protected $userModel;
    protected $attendanceModel;
    protected $leaveModel;
    protected $shiftModel;
    protected $holidayModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->attendanceModel = new Attendance();
        $this->leaveModel = new Leave();
        $this->shiftModel = new ShiftModel();
        $this->holidayModel = new HolidayModel();
    }
  public function processDailyAttendance()
    {
        //all users need to be processed for attendance 
        //for each user check if attendance record exists for today
        //if 
    }

}