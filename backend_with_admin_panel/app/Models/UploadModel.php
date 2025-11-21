<?php

namespace App\Models;

use CodeIgniter\Model;

class UploadModel extends Model
{
    protected $table = 'uploads';
    protected $primaryKey = 'id';
    protected $allowedFields = ['filename','filepath','mimetype','size','uploaded_at'];
    protected $useTimestamps = false;
}
