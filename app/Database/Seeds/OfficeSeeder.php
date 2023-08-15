<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OfficeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'test office',
                'description' => 'test office',
                'lat' => 1,
                'lng' => 1,
                'address_line_1' => 'test',
                'address_line_2' => 'test',
                'approval_status' => 1,
                'hidden' => 0,
                'price_per_day' => 10,
                'monthly_discount' => 1,
                'user_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ],
            [
                'title' => 'test office2',
                'description' => 'test office2',
                'lat' => 1,
                'lng' => 1,
                'address_line_1' => 'test2',
                'address_line_2' => 'test2',
                'approval_status' => 1,
                'hidden' => 0,
                'price_per_day' => 10,
                'monthly_discount' => 1,
                'user_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ]
        ];
        $this->db->table('offices')->insertBatch($data);
    }
}
