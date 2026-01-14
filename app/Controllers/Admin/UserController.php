<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;

class UserController extends BaseController
{
    private User $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function users()
    {
        return view('admin/users');
    }
    public function createUser()
    {
        try {
            $rules = [
                'name'     => 'required|min_length[3]|max_length[100]',
                'email'    => 'required|valid_email|is_unique[users.email]|max_length[150]',
                'password' => 'required|min_length[6]',
                'role'     => 'required|in_list[admin,user]'
            ];

            if (!$this->validate($rules)) {
                 return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }

            $data = $this->request->getJSON(true);
            
            
            $newUser = [
                'name'          => trim($data['name']),
                'email'         => strtolower(trim($data['email'])),
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role'          => $data['role'],
                'is_active'     => 1
            ];

           $result = $this->userModel->addUser($newUser);
           if(!$result){
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
           }
           
            return success(201, "User created successfully", ['csrf' => csrf_hash()]);

        } catch (\Throwable $e) {
            log_message('error', 'Create User Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
    
    public function fetchUsers()
    {
        try {
            $search = $this->request->getGet('search') ?? '';
            $role = $this->request->getGet('role') ?? '';
            $shift = $this->request->getGet('shift') ?? '';
            $status = $this->request->getGet('status') ?? '';
            $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
            $sortOrder = $this->request->getGet('sort_order') ?? 'desc';
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);
            
            $allowedSortColumns = ['name', 'email', 'role', 'shift', 'is_active', 'created_at'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
            
            $filters = [
                'search' => $search,
                'role' => $role,
                'shift' => $shift,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'page' => $page,
                'per_page' => $perPage
            ];
            
            $result = $this->userModel->getUsers($filters);
            
            return success(200, 'Users fetched successfully', [
                'users' => $result['users'],
                'pagination' => [
                    'total' => $result['total'],
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'from' => $result['from'],
                    'to' => $result['to']
                ]
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Fetch Users Error: ' . $e->getMessage());
            return error(500, 'Internal server error');
        }
    }
    
    public function updateUser($id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return error(404, 'User not found', ['csrf' => csrf_hash()]);
            }
            
            $data = $this->request->getJSON(true);
            
            $rules = [
                'name'  => 'required|min_length[3]|max_length[100]',
                'email' => "required|valid_email|is_unique[users.email,id,{$id}]|max_length[150]",
                'role'  => 'required|in_list[admin,user]'
            ];

            if (!$this->validate($rules)) {
                return error(422, 'Validation failed', [
                    'errors' => $this->validator->getErrors(),
                    'csrf'   => csrf_hash()
                ]);
            }
            
            $updateData = [
                'name'  => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'role'  => $data['role']
            ];
            
            $result = $this->userModel->update($id, $updateData);
            if (!$result) {
                return error(500, 'Failed to update user', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'User updated successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Update User Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
    
    public function deleteUser($id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return error(404, 'User not found', ['csrf' => csrf_hash()]);
            }
            
            $result = $this->userModel->update($id, ['is_active' => 0]);
            if (!$result) {
                return error(500, 'Failed to delete user', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'User deleted successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Delete User Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
    
    public function restoreUser($id)
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return error(404, 'User not found', ['csrf' => csrf_hash()]);
            }
            
            $result = $this->userModel->restore($id);
            if (!$result) {
                return error(500, 'Failed to restore user', ['csrf' => csrf_hash()]);
            }
            
            return success(200, 'User restored successfully', ['csrf' => csrf_hash()]);
            
        } catch (\Throwable $e) {
            log_message('error', 'Restore User Error: ' . $e->getMessage());
            return error(500, 'Internal server error', ['csrf' => csrf_hash()]);
        }
    }
    public function importUsers()
    {
        try {
            $file = $this->request->getFile('file');
            if (!$file || !$file->isValid()) {
                return error(400, 'Invalid file', ['csrf' => csrf_hash()]);
            }

            // Validate file extension
            $extension = $file->getExtension();
            if (!in_array(strtolower($extension), ['csv', 'txt'])) {
                return error(400, 'Only CSV files are allowed', ['csrf' => csrf_hash()]);
            }

            if ($file->getSize() > 5 * 1024 * 1024) { // 5MB limit
                return error(400, 'File is too large (max 5MB)', ['csrf' => csrf_hash()]);
            }

            $handle = fopen($file->getTempName(), 'r');
            if (!$handle) {
                return error(500, 'Could not open file', ['csrf' => csrf_hash()]);
            }

            // Get Headers
            $fileHeaders = fgetcsv($handle);
            if (!$fileHeaders) {
                fclose($handle);
                return error(400, 'File is empty', ['csrf' => csrf_hash()]);
            }

            // Map headers to column indices (normalize to lowercase)
            $headerMap = [];
            foreach ($fileHeaders as $index => $header) {
                // Remove UTF-8 BOM if present and trim
                $cleanHeader = strtolower(trim(preg_replace('/\x{FEFF}/u', '', $header)));
                $headerMap[$cleanHeader] = $index;
            }

            // Check for required columns
            $requiredColumns = ['name', 'email', 'password'];
            foreach ($requiredColumns as $col) {
                if (!isset($headerMap[$col])) {
                    fclose($handle);
                    return error(400, "Missing required column: {$col}", ['csrf' => csrf_hash()]);
                }
            }

            $inserted = 0;
            $skipped = 0;
            $rowNumber = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Extract data using header map
                $data = [];
                // Skip empty rows
                if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                    continue;
                }

                foreach ($headerMap as $key => $index) {
                    $data[$key] = isset($row[$index]) ? trim($row[$index]) : '';
                }

                $name = $data['name'] ?? '';
                $email = strtolower($data['email'] ?? '');
                $password = $data['password'] ?? '';
                $role = strtolower($data['role'] ?? 'user');
                $status = $data['status'] ?? '1';
                
                // Validate Name
                if (empty($name) || strlen($name) < 3) {
                    // Invalid name, skip row
                    continue;
                }

                // Validate Email
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Invalid email, skip row
                    continue;
                }

                // Validate Password
                if (empty($password) || strlen($password) < 6) {
                    // Invalid password, skip row
                    continue;
                }

                // Validate Role
                if (!in_array($role, ['admin', 'user'])) {
                    $role = 'user'; 
                }

                // Check for Duplicate Email in DB
                if ($this->userModel->where('email', $email)->first()) {
                    $skipped++;
                    continue;
                }

                // Insert User
                $insertData = [
                    'name'          => $name,
                    'email'         => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role'          => $role,
                    'is_active'     => (int)$status
                ];

                if ($this->userModel->insert($insertData)) {
                    $inserted++;
                }
            }

            fclose($handle);

            return success(200, 'Import process completed', [
                'inserted' => $inserted,
                'skipped'  => $skipped,
                'csrf'     => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Import User Error: ' . $e->getMessage());
            return error(500, 'Internal server error during import', ['csrf' => csrf_hash()]);
        }
    }
    public function exportUsers()
    {
        try {
            // Get filters
            $search = $this->request->getGet('search') ?? '';
            $role = $this->request->getGet('role') ?? '';
            $shift = $this->request->getGet('shift') ?? '';
            $status = $this->request->getGet('status') ?? '';
            $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
            $sortOrder = $this->request->getGet('sort_order') ?? 'desc';
            
            // Validate sort column
            $allowedSortColumns = ['name', 'email', 'role', 'shift', 'is_active', 'created_at'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
            
            // Build filters array with large limit to get all filtered users
            $filters = [
                'search' => $search,
                'role' => $role,
                'shift' => $shift,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'page' => 1,
                'per_page' => 100000 // Large limit to export all matching records
            ];
            
            $result = $this->userModel->getUsers($filters);
            $users = $result['users'];
            
            // Set headers for download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d_H-i-s') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($output, ['Name', 'Email', 'Role']);
            
            foreach ($users as $user) {
                fputcsv($output, [
                    $user['name'],
                    $user['email'],
                    $user['role']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (\Throwable $e) {
            log_message('error', 'Export Users Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export users');
        }
    }
}
