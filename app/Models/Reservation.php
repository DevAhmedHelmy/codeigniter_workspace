<?php

namespace App\Models;

use CodeIgniter\Model;

class Reservation extends Model
{

    protected $table            = 'reservations';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['office_id', 'price', 'status', 'start_date', 'end_date', 'user_id'];
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'office_id' => ['required'],

        'price' => ['required'],
        'status' => ['required'],
        'start_date' => ['required'],
        'end_date' => ['required'],
    ];

    public function getAllReservations($limit, $offset)
    {
        $builder = $this->db->table('reservations');
        $builder->select('reservations.*, users.name AS user_name, offices.title AS office_title');
        $builder->join('users', 'users.id = reservations.user_id');
        $builder->join('offices', 'offices.id = reservations.office_id');
        $builder->limit($limit, $offset);
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getReservationsCount()
    {
        return $this->db->table('reservations')->countAllResults();
    }


    public function getReservation($reservationId)
    {
        $builder = $this->db->table('reservations');
        $builder->select('reservations.*, users.name AS user_name, offices.title AS office_title');
        $builder->join('users', 'users.id = reservations.user_id');
        $builder->join('offices', 'offices.id = reservations.office_id');
        $builder->where('reservations.id', $reservationId);
        $result = $builder->get()->getRowArray();
        return $result;
    }
}
