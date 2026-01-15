<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\IpAddress;
use App\Models\Settings;

class SettingsController extends BaseController
{
    private IpAddress $ipModel;
    private Settings $settingsModel;

    public function __construct()
    {
        $this->ipModel = new IpAddress();
        $this->settingsModel = new Settings();
    }

    public function index()
    {
        return redirect()->to('admin/settings/ips');
    }

    public function ips()
    {
        return view('admin/ips');
    }

    public function timezone()
    {
        $timezone = $this->settingsModel->getSetting('app_timezone', 'Asia/Kolkata');
        return view('admin/timezone', ['currentTimezone' => $timezone]);
    }

    public function fetchIps()
    {
        try {
            $search = $this->request->getGet('search') ?? '';
            $status = $this->request->getGet('status') ?? '';
            $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
            $sortOrder = $this->request->getGet('sort_order') ?? 'desc';
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);

            $filters = [
                'search' => $search,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'page' => $page,
                'per_page' => $perPage
            ];

            $result = $this->ipModel->getIps($filters);

            return success(200, 'IP addresses fetched successfully', [
                'ips' => $result['ips'],
                'pagination' => [
                    'total' => $result['total'],
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'from' => $result['from'],
                    'to' => $result['to']
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Fetch IPs Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }

    public function createIp()
    {
        try {
            $rules = [
                'label'      => 'required|min_length[3]|max_length[50]',
                'ip_address' => 'required|valid_ip|max_length[50]|is_unique[allowed_ips.ip_address]'
            ];

            if (!$this->validate($rules)) {
                 return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            $data = $this->request->getJSON(true);
            
            $newIp = [
                'label'      => trim($data['label']),
                'ip_address' => trim($data['ip_address']),
                'is_active'  => 1
            ];

            if (!$this->ipModel->insert($newIp)) {
                return error(500, 'Failed to save IP address', ['csrf' => csrf_hash()]);
            }
           
            return success(201, "IP address added successfully", ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Create IP Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    public function updateIp($id)
    {
        try {
            $ip = $this->ipModel->find($id);
            if (!$ip) {
                return error(404, 'IP address not found', ['csrf' => csrf_hash()]);
            }
            
            $rules = [
                'label'      => 'required|min_length[3]|max_length[50]',
                'ip_address' => 'required|valid_ip|max_length[50]|is_unique[allowed_ips.ip_address,id,' . $id . ']'
            ];

            if (!$this->validate($rules)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }
            
            $data = $this->request->getJSON(true);
            $updateData = [
                'label'      => trim($data['label']),
                'ip_address' => trim($data['ip_address'])
            ];
            
            if (!$this->ipModel->updateIp($id, $updateData)) {
                return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'IP address updated successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Update IP Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    public function deleteIp($id)
    {
        try {
            $ip = $this->ipModel->find($id);
            if (!$ip) {
                return error(404, 'IP address not found', ['csrf' => csrf_hash()]);
            }
            
            if (!$this->ipModel->update($id, ['is_active' => 0])) {
                return error(500, 'Failed to deactivate IP address', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'IP address deactivated successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Delete IP Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    public function restoreIp($id)
    {
        try {
            $ip = $this->ipModel->find($id);
            if (!$ip) {
                return error(404, 'IP address not found', ['csrf' => csrf_hash()]);
            }
            
            if (!$this->ipModel->restore($id)) {
                return error(500, 'Failed to restore IP address', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'IP address restored successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Restore IP Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }

    public function updateTimezone()
    {
        try {
            $rules = [
                'timezone' => 'required'
            ];

            if (!$this->validate($rules)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            $data = $this->request->getJSON(true);
            $timezone = $data['timezone'];

            if (!$this->settingsModel->setSetting('app_timezone', $timezone)) {
                return error(500, 'Failed to update timezone', ['csrf' => csrf_hash()]);
            }

            return success(200, 'Timezone updated successfully', ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Update Timezone Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
}
