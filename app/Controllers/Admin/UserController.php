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
}
