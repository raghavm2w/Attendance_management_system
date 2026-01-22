<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LeaveType;
use App\Models\Leave;

class LeaveController extends BaseController
{
        private LeaveType $leaveTypeModel;
        private Leave $leaveModel;
        
        public function __construct()
    {
        $this->leaveTypeModel = new LeaveType();
        $this->leaveModel = new Leave();
      
    }
    public function index()
    {
        return view('user/leaves');
    }
    public function fetchLeaveTypes()
    {
        try{
           $leaves = $this->leaveTypeModel->findAll(8);
           return success(200,"leave types fetched successfully",$leaves);


        }catch (\Throwable $e) {
            log_message('error', 'fetch leave types Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
    public function submitLeaveRequest(){
        try{
            $data = $this->request->getJSON(true);   
            if(empty($data['leave_type_id'])){
                return error(422,"validation failed",['errors'=>['leave_type_id'=>'leave type is required'],'csrf_token'=>csrf_hash()]);
            }
            $rules = [
                'type' => 'required|in_list[full,half,short]',
                'start_date' => 'required|valid_date[Y-m-d]',
                'end_date' => 'permit_empty|valid_date[Y-m-d]',
                'reason' => 'required|max_length[250]',
            ];
            if ($data['type'] === 'full') {
                $rules['end_date'] = 'required|valid_date[Y-m-d]';
            }
            $messages = [
                'start_date' => [
                    'required'   => 'Please select a start date',
                    'valid_date' => 'Start date must be a valid date',
                ],
                'end_date' => [
                    'valid_date' => 'End date must be a valid date',
                ],
                'type' => [
                    'required' => 'Please select a leave duration',
                    'in_list'  => 'Invalid leave dutration selected',
                ],
            ];
            if (!$this->validate($rules,$messages)) {
                return error(422, 'Validation failed',['errors' => $this->validator->getErrors(),'csrf_token'=>csrf_hash()]);
            }
            $today = date('Y-m-d');

            if (strtotime($data['start_date']) < strtotime($today)) {
                return error(422, 'From date cannot be in the past',['errors'=>['start_date'=>'From date cannot be in the past'],'csrf_token'=>csrf_hash()]);
            }
            if($data['end_date']){
               if (strtotime($data['end_date']) < strtotime($today)) {
                return error(422, 'To date cannot be in the past',['errors'=>['end_date'=>'To date cannot be in the past'],'csrf_token'=>csrf_hash()]);
            }
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                return error(422, 'To date cannot be before from date',['errors'=>['end_date'=>'To date cannot be before from date'],'csrf_token'=>csrf_hash()]);
            }
        }
             $user_id = $_REQUEST['auth_user']['id'] ;
            $data['user_id'] = $user_id;

            //check if the user has pending or approved leaves on same dates
            $exists = $this->leaveModel->checkDuplicate($data);

        
            if($exists){
                return error(400,"You have already requested leave for these dates",['csrf_token'=>csrf_hash()]);
            }

            $data['status'] = 'pending';
            $this->leaveModel->addLeaveRequest($data);
            return success(200,"Leave request submitted successfully",['csrf_token'=>csrf_hash()]);



        }catch (\Throwable $e) {
            log_message('error', 'Submit leaves Error: ' . $e->getMessage());
            return error(500, 'Internal server error',['errors'=>[],'csrf_token'=>csrf_hash()]);
        }
    }

    public function fetchLeaves()
    {
        try {
            $userId = $_REQUEST['auth_user']['id'];
            
            $filters = [
                'search' => $this->request->getGet('search') ?? '',
                'status' => $this->request->getGet('status') ?? 'pending',
                'sort_order' => $this->request->getGet('sort_order') ?? 'desc',
                'page' => (int)($this->request->getGet('page') ?? 1),
                'per_page' => (int)($this->request->getGet('per_page') ?? 10),
            ];
            
            $result = $this->leaveModel->fetchUserLeaves($userId, $filters);
            
            return success(200, 'Leaves fetched successfully', [
                'leaves' => $result['leaves'],
                'pagination' => [
                    'total' => $result['total'],
                    'from' => $result['from'],
                    'to' => $result['to'],
                    'current_page' => $result['current_page'],
                    'per_page' => $result['per_page']
                ]
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Fetch leaves Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }

}
