<?php namespace App\Controllers\Api\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class Auth extends BaseController
{
    public function login()
    {
        $body = $this->request->getJSON(true);
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        if (!$email || !$password) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'email and password required']);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Invalid credentials']);
        }

        $payload = [
            'iss' => getenv('JWT_ISSUER') ?: 'ecommerce.local',
            'aud' => getenv('JWT_AUD') ?: 'ecommerce_clients',
            'iat' => time(),
            'exp' => time() + ((int)getenv('JWT_EXP_SECONDS') ?: 3600),
            'sub' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        $token = JWT::encode($payload, getenv('JWT_SECRET') ?: 'secret-example', 'HS256');

        return $this->response->setJSON([
            'token' => $token,
            'user'  => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }
}
