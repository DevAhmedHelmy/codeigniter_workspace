<?php

namespace App\Models;

use CodeIgniter\Model;

class Token extends Model
{
    const HASH_ALGORITHM = 'HS256';

    protected $DBGroup          = 'default';
    protected $table            = 'tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','token','email', 'expiration_time', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    

 
    
}
