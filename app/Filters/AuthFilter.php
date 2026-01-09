<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use App\Models\User;
use App\Models\Refresh;
use Exception;
use Firebase\JWT\ExpiredException;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during normal execution.
     * However, when an abnormal state is found, it should return an instance of
     * the ResponseInterface. If it does, then the execution will stop and that
     * Response will be sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return ResponseInterface|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $accessToken = $request->getCookie('access_token');
        $refreshToken = $request->getCookie('refresh_token');
                    log_message('info', 'fdgdfgdg');

        if (empty($accessToken)) {
            if (!empty($refreshToken)) {
                return $this->refreshTokens($request, $refreshToken);
            }
            log_message('info', 'empty refresh');

            return redirect()->to('/login');
        }

        try {
            $decoded = verifyToken($accessToken);
           
            $userData = (array) $decoded->data;
            $_REQUEST['auth_user'] = $userData;

        } catch (ExpiredException $e) {
            if (!empty($refreshToken)) {
                return $this->refreshTokens($request, $refreshToken);
            }
                        log_message('info', 'expired refresh');

            return redirect()->to('/login');
        } catch (Exception $e) {
            log_message('error', 'JWT Verification Error: ' . $e->getMessage());
            if (!empty($refreshToken)) {
                return $this->refreshTokens($request, $refreshToken);
            }

            return redirect()->to('/login');
        }
    }

    private function refreshTokens(RequestInterface $request, string $token)
    {
        try {
            $decoded = verifyToken($token);
            $userId = $decoded->data->id;

            $refreshModel = new Refresh();
            $validToken = $refreshModel->validateRefreshToken($userId, $token);

            if (!$validToken) {
                            log_message('info', 'not valid refresh');

                return redirect()->to('/login');
            }

            $userModel = new User();
            $user = $userModel->find($userId);

            if (!$user || $user['is_active'] != 1) {
                            log_message('info', 'no user');

                return redirect()->to('/login');
            }

            // Generate NEW Tokens
            $newAccessToken = generateAccessToken([
                'id'    => $user['id'],
                'role'  => $user['role'],
                'email' => $user['email']
            ]);
            $newRefreshToken = generateRefreshToken(['id' => $user['id']]);

            // Update DB: Delete old, add new
            $refreshModel->deleteRefreshToken($token);
            $refreshModel->addRefreshToken($user['id'], $newRefreshToken);

            // Set Cookies
            $response = Services::response();
            
            $response->setCookie([
                'name'     => 'access_token',
                'value'    => $newAccessToken,
                'expire'   => (int) getenv('JWT_ACCESS_TTL'),
                'httponly' => true,
                'secure'   => isset($_SERVER['HTTPS']),
                'samesite' => 'Lax'
            ]);

            $response->setCookie([
                'name'     => 'refresh_token',
                'value'    => $newRefreshToken,
                'expire'   => (int) getenv('JWT_REFRESH_TTL'),
                'httponly' => true,
                'secure'   => isset($_SERVER['HTTPS']),
                'samesite' => 'Lax'
            ]);

            $_REQUEST['auth_user'] = [
                'id'    => $user['id'],
                'role'  => $user['role'],
                'email' => $user['email']
            ];

            // In CI4 filters, to continue with modified response (cookies), 
            // we usually don't return anything, but we need to ensure the cookies are sent.
            // Actually, if we want to proceed and have the cookies set, we should just let it through.
            // But CI4 doesn't automatically merge the response object we just created into the final response 
            // unless we return it, but returning it STOPS the execution.
            // The correct way in CI4 is to use the global response object.
            
            return null; // Proceed to controller

        } catch (Exception $e) {
            log_message('error', 'Refresh Token Error: ' . $e->getMessage());
            return redirect()->to('/login');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
