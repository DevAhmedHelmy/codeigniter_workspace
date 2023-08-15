<?php

namespace App\Models;

use CodeIgniter\Model;

class Tag extends Model
{

    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['name'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
 

    // Validation
    protected $validationRules      = ['name' => 'required'];


    public function deleteTagsFromPivot($id)
    {
        $deleted = $this->db->table('offices_tags')->delete(['tag_id' => $id]);
        if ($deleted)  $this->db->table('tags')->delete(['id' => $id]);
        return $deleted;
    }
}
