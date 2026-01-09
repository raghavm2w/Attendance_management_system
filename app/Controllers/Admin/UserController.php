<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;

class UserController extends BaseController
{
    private User $user;
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
                'name'          => $data['name'],
                'email'         => $data['email'],
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
}
