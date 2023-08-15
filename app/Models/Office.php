<?php

namespace App\Models;

use CodeIgniter\Model;

class Office extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'offices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
 
    protected $allowedFields    = ['title', 'description', 'lat', 'lng', 'address_line_1', 'address_line_2', 'approval_status', 'hidden', 'price_per_day', 'monthly_discount', 'user_id', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
 

    // Validation
    protected $validationRules      = [
        'title' => ['required'],
        'description' => ['required'],
        'lat' => ['required'],
        'lng' => ['required'],
        'address_line_1' => ['required'],
        'address_line_2' => ['permit_empty'],
        'approval_status' => ['required'],
        'hidden' => ['required'],
        'price_per_day' => ['required'],
        'monthly_discount' => ['permit_empty'],
        'tags' => ['permit_empty'],
        'tags.*' => ['permit_empty', 'integer'],
    ];
    

 

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'office_tags', 'office_id', 'tag_id');
    }

    public function syncTags($tags, $office_id)
    {
        $this->db->table('offices_tags')->where('office_id', $office_id)->delete();
        $data = [];
        foreach ($tags as $tag) {
            $data[] = [
                'office_id' => $office_id,
                'tag_id' => $tag
            ];
        }
        return $this->db->table('offices_tags')->insertBatch($data);
    }

    public function deleteTagsByOfficeId($office_id)
    {
        return $this->db->table('offices_tags')->where('office_id', $office_id)->delete();
    }

    public function getOfficeWithTags($officeId)
    {
        $builder = $this->db->table('offices');
        $builder->select('offices.*, users.name AS user_name, GROUP_CONCAT(tags.name) AS tags');
        $builder->join('users', 'users.id = offices.user_id');
        $builder->join('offices_tags', 'offices_tags.office_id = offices.id');
        $builder->join('tags', 'tags.id = offices_tags.tag_id');
        $builder->where('offices.id', $officeId);
        $result = $builder->get()->getRowArray();

        if ($result) {
            $result['tags'] = explode(',', $result['tags']);
            return $result;
        }
        return null;
    }


    public function getOfficesWithTags($limit, $offset)
    {
        $builder = $this->db->table('offices');
        $builder->select('offices.*, users.name AS user_name, GROUP_CONCAT(tags.name) AS tags');
        $builder->join('users', 'users.id = offices.user_id');
        $builder->join('offices_tags', 'offices_tags.office_id = offices.id', 'left');
        $builder->join('tags', 'tags.id = offices_tags.tag_id', 'left');
        $builder->groupBy('offices.id');
        $builder->limit($limit, $offset);
        $result = $builder->get()->getResultArray();

        foreach ($result as &$office) {
            $office['tags'] = !empty($office['tags']) ? explode(',', $office['tags']) : [];
        }

        return $result;
    }

    public function getOfficesCount()
    {
        return $this->db->table('offices')->countAllResults();
    }
}
