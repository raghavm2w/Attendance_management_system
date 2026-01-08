<?php
use CodeIgniter\HTTP\ResponseInterface;

if (!function_exists('success')) {
    function success(
        int $code = 200,
        string $message = 'Success',
        $data = null
    ) {
        $response = service('response');

        return $response
            ->setStatusCode($code)
            ->setJSON([
                'status'  => 'success',
                'message' => $message,
                'data'    => $data
            ]);
    }
}

if (!function_exists('error')) {
    function error(
        int $code = 400,
        string $message = 'Error',
        $errors = null
    ) {
        $response = service('response');

        return $response
            ->setStatusCode($code)
            ->setJSON([
                'status'  => 'error',
                'message' => $message,
                'errors'  => $errors
            ]);
    }
}