<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Shift;
use App\Models\IpAddress;
use App\Models\Settings;
use App\Models\Attendance;
use App\Models\Leave;





class HomeController extends BaseController
{
    private Shift $shiftModel;
    private IpAddress $ipModel;
    private Settings $settings;
    private Attendance $attendance;
    private Leave $leaveModel;



     public function __construct()
    {
        $this->shiftModel = new Shift();
        $this->ipModel = new  IpAddress();
        $this->settings = new  Settings();
        $this->attendance = new  Attendance();
        $this->leaveModel = new Leave();

    }
    public function index()
    {
        $user_id = $_REQUEST['auth_user']['id'];
        $userShift = $this->shiftModel->getShiftByUserId($user_id);
        $timezone = $this->settings->getSetting('app_timezone');
        $userShift[0]['start_time'] = fromUtcTime($userShift[0]['start_time'], $timezone);
        $userShift[0]['end_time'] = fromUtcTime($userShift[0]['end_time'], $timezone);
         return view('user/dashboard',["shift"=>$userShift[0]]);
    }
    public function getUserShift(){
        try{
            $user_id = $_REQUEST['auth_user']['id'] ;
            $userShift = $this->shiftModel->getShiftByUserId($user_id);
            $timezone = $this->settings->getSetting('app_timezone');
            $userShift[0]['start_time'] = fromUtcTime($userShift[0]['start_time'], $timezone);
            $userShift[0]['end_time'] = fromUtcTime($userShift[0]['end_time'], $timezone);
            return success(200,"user shift fetched",$userShift);

        }catch (\Throwable $e) {
            log_message('error', 'Get user shift Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
    // use check in 
    public function checkIn(){
        try{
            $user_id = $_REQUEST['auth_user']['id'] ;
            $userIp = $this->request->getIPAddress();// ger user ip address
            $validIp = $this->ipModel->isAllowed($userIp);
            if(!$validIp){
                return error(403,"user is not connected to office internet");
            }
            //get the system timezone
            $timezone = $this->settings->getSetting('app_timezone');
            $checkInTime = convertToUTC($timezone);

            //insert into the db
            $data = [
                'user_id'=>$user_id,
                'check_in'=>$checkInTime,
            
        ];
            $this->attendance->checkIn($data);
           

            $realcheckInTime = convertFromUTC($checkInTime,$timezone);
            return success(200,"success chekin",$realcheckInTime);




        }
        catch (\Throwable $e) {
            log_message('error', 'User Check in Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
    public function checkOut(){
        try{
             $user_id = $_REQUEST['auth_user']['id'] ;
            $userIp = $this->request->getIPAddress();// ger user ip address
            $validIp = $this->ipModel->isAllowed($userIp);
            if(!$validIp){
                return error(403,"user is not connected to office internet");
            }
            //get the system timezone
            $timezone = $this->settings->getSetting('app_timezone');
            $checkOutTime = convertToUTC($timezone);
            $data = [
                'user_id'=>$user_id,
                'check_out'=>$checkOutTime,
            
            ];
            $this->attendance->checkOut($data);
            //----. check for company off days and leaves of user ---(not checking it now because user wont come)
            //-----. check shift working hours and grace time
            // calculate using required minutes and worked minutes
            
            // 1... fetch the half day leaves for the user on the present day
           $halfLeave =  $this->leaveModel->getHalfLeave($user_id);
            // fetch the shift for user from user_shifts and shifts table
            $shift = $this->shiftModel->getShiftByUserId($user_id);
            $requiredMinutes = $this->calculateShiftMinutes($shift['start_time'],$shift['end_time']);
            if($halfLeave){
                $requiredMinutes = $requiredMinutes/2;
            }
            //get checkin time and checkout to calculate worked minutes of user
           $attendance = $this->attendance->getCheckIn($user_id,$checkOutTime);
           $checkInTime = $attendance['check_in'];
           $workedMinutes = $this->calculateWorkedMinutes($checkInTime,$checkOutTime);
              if ($workedMinutes >= $requiredMinutes) {
                $status = 1; // 'present';
            } elseif ($workedMinutes >= ($requiredMinutes / 2)) {
                $status = 2; //'half_day';
            } else {
                $status = 0; //'absent';
            }
            //update attendance status---
            $this->attendance->updateStatus($user_id,$attendance['id'],$status);
            $realcheckOutTime = convertFromUTC($checkOutTime,$timezone);
            return success(200,"success checkout",
            [
                "check_out"=>$realcheckOutTime,"worked_minutes"=>$workedMinutes,"required_minutes"=>$requiredMinutes,"status"=>$status
            ]);
            

        }  catch (\Throwable $e) {
            log_message('error', 'User Check out Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
    // helper for calculating shift minutes
        private function calculateShiftMinutes(string $start, string $end): int
    {
        $startTime = new \DateTime($start);
        $endTime   = new \DateTime($end);

        return ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;
    }
    //helper for calculating worked minutes
        private function calculateWorkedMinutes(string $checkInUtc, string $checkOutUtc): int
    {
        $in  = new \DateTime($checkInUtc, new \DateTimeZone('UTC'));
        $out = new \DateTime($checkOutUtc, new \DateTimeZone('UTC'));

        if ($out <= $in) {
            return 0;
        }

        return (int) (($out->getTimestamp() - $in->getTimestamp()) / 60);
    }
}
