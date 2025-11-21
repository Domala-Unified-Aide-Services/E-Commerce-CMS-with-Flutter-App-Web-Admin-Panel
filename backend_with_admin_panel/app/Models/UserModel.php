<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'created_at'
    ];

    /**
     * Find a user using email
     * Used in Auth::login()
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
