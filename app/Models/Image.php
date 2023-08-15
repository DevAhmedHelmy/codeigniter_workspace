<?php

namespace App\Models;

use CodeIgniter\Model;

class Image extends Model
{

    protected $table            = 'images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['resource', 'resource_id', 'path', 'file_name', 'file_size', 'file_type', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    public static function getImages($resource, $resource_id, $id = null)
    {
        if ($id) {
            return self::where('resource', $resource)
                ->where('resource_id', $resource_id)
                ->where('id', $id)
                ->first();
        }
        return self::where('resource', $resource)
            ->where('resource_id', $resource_id)
            ->findAll();
    }
}
