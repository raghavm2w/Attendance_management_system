<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;
use App\Models\Refresh;


class AuthController extends BaseController
{
     protected $userModel;
          protected $refreshModel;


    public function __construct()
    {
        $this->userModel = new User();
        $this->refreshModel = new Refresh();

    }
    public function index()
    {
         return view('login');
    }
     public function login()
    {
        try{
        //      if ($this->request->getMethod() !== 'post') {
        //     return error(400,"invalid request");
        // }
          $rules = [
            'email' => 'required|valid_email|max_length[100]|regex_match[/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return error(422, 'Validation failed', [
                'errors' => $this->validator->getErrors(),
                'csrf'   => csrf_hash()
            ]);
        }
                $data = $this->request->getJSON(true);   

        $email = $data['email'];
        $pass = $data['password'];
        $user = $this->userModel->findByEmail($email);
        if(!$user){
            return error(404,"User not found !",[ 'csrf' => csrf_hash()]);
        }
        if(!password_verify($pass,$user['password_hash'])){
            return error(400,"Incorrect password", ['csrf' => csrf_hash()]);
        }
          $accessToken = generateAccessToken([
            'id'    => $user['id'],
            'role'  => $user['role'],
            'email' => $user['email']
        ]);
        $refreshToken = generateRefreshToken(['id'=>$user['id']]);
        $res = $this->refreshModel->addRefreshToken($user['id'],$refreshToken);
        if(!$res){
            log_message("error","failed to set refresh token in db");
            return error(500,"internal server error in login",[ 'csrf' => csrf_hash()]);
        }
        $this->response->setCookie([
            'name'     => 'access_token',
            'value'    => $accessToken,
            'expire'   => (int) getenv('JWT_ACCESS_TTL'),
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']),
            'samesite' => 'Lax'
        ]);

        $this->response->setCookie([
            'name'     => 'refresh_token',
            'value'    => $refreshToken,
            'expire'   => (int) getenv('JWT_REFRESH_TTL'),
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']),
            'samesite' => 'Lax'
        ]);
        return success(200,"login successful",["role"=>$user['role'], "csrf" => csrf_hash()]);

        }catch (\Throwable $e) {
            log_message('error', "login error :".$e->getMessage());
            return error(500, 'Internal server error',[ 'csrf' => csrf_hash()]);
        }
    }
    public function logout()
    {
        try {
            $refreshToken = $this->request->getCookie('refresh_token');

            if ($refreshToken) {
                // Delete the refresh token from the database
                $this->refreshModel->deleteRefreshToken($refreshToken);
            }

            // Clear Cookies
            $this->response->setCookie([
                'name'     => 'access_token',
                'value'    => '',
                'expire'   => -3600,
                'path'     => '/',
                'httponly' => true,
                'secure'   => isset($_SERVER['HTTPS']),
                'samesite' => 'Lax'
            ]);

            $this->response->setCookie([
                'name'     => 'refresh_token',
                'value'    => '',
                'expire'   => -3600,
                'path'     => '/',
                'httponly' => true,
                'secure'   => isset($_SERVER['HTTPS']),
                'samesite' => 'Lax'
            ]);

            return success(200, "Logged out successfully");

        } catch (\Throwable $e) {
            log_message('error', "logout error: " . $e->getMessage());
            return error(500, 'Internal server error during logout');
        }
    }
}
