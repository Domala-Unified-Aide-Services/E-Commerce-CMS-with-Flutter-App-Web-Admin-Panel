<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $helpers = ['form', 'url', 'security'];

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        // If already logged in as admin, redirect to admin root
        if (session()->get('is_admin')) {
            return redirect()->to(base_url('admin'));
        }

        echo view('admin/header');
        echo view('admin/login');
        echo view('admin/footer');
    }

    public function attempt()
    {
        $post = $this->request->getPost();
        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';

        if ($email === '' || $password === '') {
            return redirect()->back()->with('error', 'Please provide email and password.');
        }

        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            log_message('debug', "Admin login failed - user not found for email: {$email}");
            return redirect()->back()->with('error', 'Invalid credentials.');
        }

        $stored = $user['password'] ?? '';

        // Determine whether stored password is a hash (bcrypt starts with $2y$ or $2b$)
        $isHash = (strpos($stored, '$2y$') === 0) || (strpos($stored, '$2b$') === 0) || (strpos($stored, '$argon2') === 0);

        $passwordOk = false;

        if ($isHash) {
            // hashed password path (recommended)
            if (password_verify($password, $stored)) {
                $passwordOk = true;
            }
        } else {
            // plaintext path (insecure) — fallback for existing rows
            if ($password === $stored) {
                $passwordOk = true;
            }
        }

        if (!$passwordOk) {
            log_message('debug', "Admin login failed - bad password for email: {$email}. isHash=" . ($isHash ? '1' : '0'));
            return redirect()->back()->with('error', 'Invalid credentials.');
        }

        // role check
        if (!isset($user['role']) || $user['role'] !== 'admin') {
            log_message('debug', "Admin login attempt by non-admin user: {$email}, role={$user['role']}");
            return redirect()->back()->with('error', 'You are not authorized to access admin panel.');
        }

        // Set session
        session()->set([
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'user_name' => $user['username'] ?? null,
            'is_admin' => true,
            'logged_in' => true,
        ]);

        log_message('debug', "Admin login success: {$email} (id={$user['id']})");

        return redirect()->to(base_url('admin'));
    }

    public function logout()
    {
        session()->remove(['user_id','user_email','user_name','is_admin','logged_in']);
        session()->destroy();
        return redirect()->to(base_url('admin/login'));
    }
}
    