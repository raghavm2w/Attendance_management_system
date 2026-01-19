<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Leave;

class LeaveController extends BaseController
{
    protected Leave $leaveModel;

    public function __construct()
    {
        $this->leaveModel = new Leave();
    }

    public function index()
    {
        return view('admin/leaves');
    }

    public function fetchLeaves()
    {
        try {
            $filters = [
                'search' => $this->request->getGet('search') ?? '',
                'status' => $this->request->getGet('status') ?? 'pending',
                'applied_from' => $this->request->getGet('applied_from') ?? '',
                'applied_to' => $this->request->getGet('applied_to') ?? '',
                'leave_from' => $this->request->getGet('leave_from') ?? '',
                'leave_to' => $this->request->getGet('leave_to') ?? '',
                'sort_by' => $this->request->getGet('sort_by') ?? 'created_at',
                'sort_order' => $this->request->getGet('sort_order') ?? 'desc',
                'page' => (int)($this->request->getGet('page') ?? 1),
                'per_page' => (int)($this->request->getGet('per_page') ?? 10),
            ];

            $result = $this->leaveModel->fetchAllLeaves($filters);

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
            log_message('error', 'Fetch admin leaves Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }

    public function approveLeave($id)
    {
        try {
            $leave = $this->leaveModel->find($id);
            
            if (!$leave) {
                return error(404, 'Leave request not found', ['csrf' => csrf_hash()]);
            }

            if ($leave['status'] !== 'pending') {
                return error(400, 'Only pending leaves can be approved', ['csrf' => csrf_hash()]);
            }

            $this->leaveModel->updateStatus((int)$id, 'approved');

            return success(200, 'Leave request approved successfully', ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Approve leave Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    public function rejectLeave($id)
    {
        try {
            $leave = $this->leaveModel->find($id);
            
            if (!$leave) {
                return error(404, 'Leave request not found', ['csrf' => csrf_hash()]);
            }

            if ($leave['status'] !== 'pending') {
                return error(400, 'Only pending leaves can be rejected', ['csrf' => csrf_hash()]);
            }

            $this->leaveModel->updateStatus((int)$id, 'rejected');

            return success(200, 'Leave request rejected successfully', ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Reject leave Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
}
