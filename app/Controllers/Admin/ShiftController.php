<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserShift;

class ShiftController extends BaseController
{
    private Shift $shiftModel;
    private UserShift $userShiftModel;
    
    public function __construct()
    {
        $this->shiftModel = new Shift();
        $this->userShiftModel = new UserShift();

    }
    
    public function index()
    {
        return view('admin/shifts');
    }
      public function userShifts()
    {
        $shifts = $this->shiftModel->findAll();
        return view('admin/user_shifts', ['shifts' => $shifts]);
    }

    public function createShift()
    {
        try {
            $rules = [
                'type'       => 'required|min_length[2]|max_length[50]',
                'start_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
                'end_time'   => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
                'grace_time' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[120]'
            ];

            if (!$this->validate($rules)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            $data = $this->request->getJSON(true);
            
            $newShift = [
                'type'       => $data['type'],
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'grace_time' => (int) $data['grace_time']
            ];

            $result = $this->shiftModel->addShift($newShift);
            if (!$result) {
                return error(500, 'Failed to create shift', ['csrf' => csrf_hash()]);
            }
            
            return success(201, 'Shift created successfully', ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Create Shift Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
    
    public function fetchShifts()
    {
        try {
            $search = $this->request->getGet('search') ?? '';
            $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
            $sortOrder = $this->request->getGet('sort_order') ?? 'desc';
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);
            
            $allowedSortColumns = ['type', 'start_time', 'end_time', 'grace_time', 'created_at'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
            
            $filters = [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'page' => $page,
                'per_page' => $perPage
            ];
            
            $result = $this->shiftModel->getShifts($filters);
            
            return success(200, 'Shifts fetched successfully', [
                'shifts' => $result['shifts'],
                'pagination' => [
                    'total' => $result['total'],
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'from' => $result['from'],
                    'to' => $result['to']
                ]
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Fetch Shifts Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
    
    public function updateShift($id)
    {
        try {
            $shift = $this->shiftModel->find($id);
            if (!$shift) {
                return error(404, 'Shift not found', ['csrf' => csrf_hash()]);
            }
            
            $rules = [
                'type'       => 'required|min_length[2]|max_length[50]',
                'start_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
                'end_time'   => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
                'grace_time' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[120]'
            ];

            if (!$this->validate($rules)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }
            
            $data = $this->request->getJSON(true);
            
            $updateData = [
                'type'       => $data['type'],
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'grace_time' => (int) $data['grace_time']
            ];
            
            $result = $this->shiftModel->update($id, $updateData);
            if (!$result) {
                return error(500, 'Failed to update shift', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'Shift updated successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Update Shift Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
    
    public function deleteShift($id)
    {
        try {
            $shift = $this->shiftModel->find($id);
            if (!$shift) {
                return error(404, 'Shift not found', ['csrf' => csrf_hash()]);
            }
            
            $result = $this->shiftModel->delete($id);
            if (!$result) {
                return error(500, 'Failed to delete shift', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'Shift deleted successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Delete Shift Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

  
    public function assignShift()
    {
        try {
            $rules = [
                'email'    => 'required|valid_email',
                'shift_id' => 'required|integer|is_not_unique[shifts.id]'
            ];

            if (!$this->validate($rules)) {
                 return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }
            $data = $this->request->getJSON(true);

            $email = trim($data['email']);
            $shiftId = (int) $data['shift_id'];

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if (!$user) {
                return error(404, 'User not found', ['csrf' => csrf_hash()]);
            }

            $message = $this->userShiftModel->assignShiftToUser($user['id'], $shiftId);
            

            return success(200, "shift assigned successfully", ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Assign Shift Error: ' . $e->getMessage());
            return error(500, 'An error occurred while assigning shift', ['csrf' => csrf_hash()]);
        }
    }

    public function bulkAssignShift()
    {
        try {
            $rules = [
                'user_ids' => 'required',
                'shift_id' => 'required|integer|is_not_unique[shifts.id]'
            ];

            if (!$this->validate($rules)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            $data = $this->request->getJSON(true);
            $userIds = $data['user_ids'];
            $shiftId = (int) $data['shift_id'];

            if (!is_array($userIds) || empty($userIds)) {
                return error(422, 'No users selected', ['csrf' => csrf_hash()]);
            }

            $successCount = 0;
            foreach ($userIds as $userId) {
                $this->userShiftModel->assignShiftToUser((int)$userId, $shiftId);
                $successCount++;
            }

            return success(200, "Shift assigned to {$successCount} users successfully", ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Bulk Assign Shift Error: ' . $e->getMessage());
            return error(500, 'An error occurred while bulk assigning shifts', ['csrf' => csrf_hash()]);
        }
    }
}
