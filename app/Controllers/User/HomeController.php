<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Shift;
use App\Models\IpAddress;
use App\Models\Settings;
use App\Models\Attendance;




class HomeController extends BaseController
{
    private Shift $shiftModel;
    private IpAddress $ipModel;
    private Settings $settings;
    private Attendance $attendance;


     public function __construct()
    {
        $this->shiftModel = new Shift();
        $this->ipModel = new  IpAddress();
        $this->settings = new  Settings();
        $this->attendance = new  Attendance();


    }
    public function index()
    {
          $user_id = $_REQUEST['auth_user']['id'] ;
        $userShift = $this->shiftModel->getShiftByUserId($user_id);

         return view('user/dashboard',["shift"=>$userShift[0]]);
    }
    public function getUserShift(){
        try{
            $user_id = $_REQUEST['auth_user']['id'] ;
            $userShift = $this->shiftModel->getShiftByUserId($user_id);
            return success(200,"user shift fetched",$userShift);

        }catch (\Throwable $e) {
            log_message('error', 'Get user shift Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
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
            //----1. check for company off days and leaves of user
            //-----2. check shift working hours and grace time
            // calculate using required minutes and worked minutes
            //             if ($workedMinutes >= $requiredMinutes) {
            //     status = 'present';
            // } elseif ($workedMinutes >= ($requiredMinutes / 2)) {
            //     status = 'half_day';
            // } else {
            //     status = 'absent';
            // }


        }  catch (\Throwable $e) {
            log_message('error', 'User Check out Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
}
