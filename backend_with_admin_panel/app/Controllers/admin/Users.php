<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;
    protected $helpers = ['form','url'];

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel->orderBy('id','DESC')->findAll();
        echo view('admin/header');
        echo view('admin/users_list', $data);
        echo view('admin/footer');
    }

    public function create()
    {
        echo view('admin/header');
        echo view('admin/user_form');
        echo view('admin/footer');
    }

    public function store()
    {
        $post = $this->request->getPost();
        $username = $post['username'] ?? null;
        $email = $post['email'] ?? null;
        $password = $post['password'] ?? null;
        $role = $post['role'] ?? 'user';

        if (!$username || !$email || !$password) {
            return redirect()->back()->with('error','Please fill all required fields.');
        }

        // Hash the password for new users (recommended)
        $storedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->userModel->insert([
            'username' => $username,
            'email' => $email,
            'password' => $storedPassword,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/users'))->with('success','User created.');
    }

    public function edit($id = null)
    {
        $data['user'] = $this->userModel->find($id);
        if (!$data['user']) return redirect()->back()->with('error','User not found.');
        echo view('admin/header');
        echo view('admin/user_form', $data);
        echo view('admin/footer');
    }

    public function update($id = null)
    {
        $post = $this->request->getPost();
        $username = $post['username'] ?? null;
        $email = $post['email'] ?? null;
        $role = $post['role'] ?? 'user';

        $data = [
            'username' => $username,
            'email' => $email,
            'role' => $role,
        ];

        if (!empty($post['password'])) {
            $data['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);
        return redirect()->to(base_url('admin/users'))->with('success','User updated.');
    }

    public function delete($id = null)
    {
        $this->userModel->delete($id);
        return redirect()->back()->with('success','User deleted.');
    }
}
