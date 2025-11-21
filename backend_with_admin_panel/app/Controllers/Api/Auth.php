<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends BaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    // ============================================================
    // 🔹 REGISTER USER (POST /api/auth/register)
    // ============================================================
    public function register()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'permit_empty|in_list[admin,user]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),

            'role'     => $this->request->getVar('role') ?? 'user',
        ];

        $this->userModel->insert($data);

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'User registered successfully',
        ]);
    }

    // ============================================================
    // 🔹 LOGIN USER (POST /api/auth/login)
    // ============================================================
    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('email', $email)->first();

        if (!$user || $password !== $user['password']) {

            return $this->respond([
                'status' => 'error',
                'message' => 'Invalid email or password',
            ], 401);
        }

        // 🔐 Generate JWT Token
        $key = getenv('JWT_SECRET') ?: 'my_secret_key_123';
        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour expiry
            'uid' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
            ]
        ]);
    }

    // ============================================================
    // 🔹 FETCH ALL USERS (GET /api/auth/users)
    // ============================================================
    public function users()
    {
        $users = $this->userModel->findAll();

        // hide passwords before sending
        $cleanUsers = array_map(function ($u) {
            unset($u['password']);
            return $u;
        }, $users);

        return $this->respond([
            'status' => 'success',
            'data' => $cleanUsers
        ]);
    }

    // ============================================================
    // 🔹 VERIFY TOKEN (optional utility)
    // ============================================================
    public function verifyToken($token)
    {
        try {
            $key = getenv('JWT_SECRET') ?: 'my_secret_key_123';
            return JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}