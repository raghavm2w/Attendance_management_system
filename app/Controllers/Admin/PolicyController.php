<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LeaveType;
use App\Models\Holiday;
use App\Models\WeeklyOff;

class PolicyController extends BaseController
{
    protected $leaveTypeModel;
    protected $holidayModel;
    protected $weeklyOffModel;

    public function __construct()
    {
        $this->leaveTypeModel = new LeaveType();
        $this->holidayModel = new Holiday();
        $this->weeklyOffModel = new WeeklyOff();
    }

    public function leaves()
    {
        try {
            if ($this->request->isAJAX()) {
                $leaves = $this->leaveTypeModel->findAll();
                return success(200, 'Leave types fetched successfully', [
                    'data' => $leaves
                ]);
            }
            return view('admin/leave_policy', ['title' => 'Leave Policy']);
        } catch (\Throwable $e) {
            log_message('error', 'Fetch Leaves Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }

    public function holiday()
    {
        try {
            if ($this->request->isAJAX()) {
                $holidays = $this->holidayModel->orderBy('holiday_date', 'ASC')->findAll();
                $weeklyOffs = $this->weeklyOffModel->findAll();
                return success(200, 'Policy data fetched successfully', [
                    'holidays' => $holidays,
                    'weekly_offs' => $weeklyOffs
                ]);
            }
            return view('admin/holiday_policy', ['title' => 'Holiday Policy']);
        } catch (\Throwable $e) {
            log_message('error', 'Fetch Holiday Data Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }

    /**
     * Store a newly created leave type
     */
    public function storeLeaveType()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $data = $this->request->getPost();
            $data['carry_forward'] = $this->request->getPost('carry_forward') ? 1 : 0;
            
            if (!$this->leaveTypeModel->save($data)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->leaveTypeModel->errors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            return success(201, 'Leave type created successfully', ['csrf' => csrf_hash()]);
        } catch (\Throwable $e) {
            log_message('error', 'Store Leave Type Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    /**
     * Update the specified leave type
     */
    public function updateLeaveType($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $leaveType = $this->leaveTypeModel->find($id);
            if (!$leaveType) {
                return error(404, 'Leave type not found', ['csrf' => csrf_hash()]);
            }

            $data = $this->request->getPost();
            $data['id'] = $id;
            $data['carry_forward'] = $this->request->getPost('carry_forward') ? 1 : 0;

            if (!$this->leaveTypeModel->save($data)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->leaveTypeModel->errors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            return success(200, 'Leave type updated successfully', ['csrf' => csrf_hash()]);
        } catch (\Throwable $e) {
            log_message('error', 'Update Leave Type Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    /**
     * Delete the specified leave type
     */
    public function deleteLeaveType($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $leaveType = $this->leaveTypeModel->find($id);
            if (!$leaveType) {
                return error(404, 'Leave type not found', ['csrf' => csrf_hash()]);
            }

            if (!$this->leaveTypeModel->delete($id)) {
                return error(500, 'Failed to delete leave type', ['csrf' => csrf_hash()]);
            }

            return success(200, 'Leave type deleted successfully', ['csrf' => csrf_hash()]);
        } catch (\Throwable $e) {
            log_message('error', 'Delete Leave Type Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    /**
     * Update weekly off settings
     */
    public function updateWeeklyOff()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $days = $this->request->getPost('days') ?? [];
            
            if (!$this->weeklyOffModel->updateOffDay($days)) {
            return error(500, 'Failed to update weekly offs',['csrf' => csrf_hash()]);
            }

            return success(200, 'Weekly offs updated successfully', ['csrf' => csrf_hash()]);
        } catch (\Throwable $e) {
            log_message('error', 'Update Weekly Off Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    /**
     * Store/Update holiday
     */
    public function saveHoliday()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $id = $this->request->getPost('id');
            $data = [
                'holiday_date' => $this->request->getPost('holiday_date'),
                'name'         => $this->request->getPost('name'),
                'is_optional'  => $this->request->getPost('is_optional') ?? 0
            ];

            if ($id) {
                $data['id'] = $id;
                $holiday = $this->holidayModel->find($id);
                if (!$holiday) {
                    return error(404, 'Holiday not found', ['csrf' => csrf_hash()]);
                }
            }

            if (!$this->holidayModel->save($data)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->holidayModel->errors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            return success(200, $id ? 'Holiday updated successfully' : 'Holiday added successfully', ['csrf' => csrf_hash()]);
        } catch (\Throwable $e) {
            log_message('error', 'Save Holiday Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    /**
     * Delete holiday
     */
    public function deleteHoliday($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403);
            }

            $holiday = $this->holidayModel->find($id);
            if (!$holiday) {
                return error(404, 'Holiday not found', ['csrf' => csrf_hash()]);
            }

            if (!$this->holidayModel->delete($id)) {
                return error(500, 'Failed to delete holiday', ['csrf' => csrf_hash()]);
            }

            return success(200, 'Holiday deleted successfully', ['csrf' => csrf_hash()]);
        } catch (\Throwable $e) {
            log_message('error', 'Delete Holiday Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
}
