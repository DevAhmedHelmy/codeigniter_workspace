<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\Migration;

class CreateOfficesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'lat' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 8',
            ],
            'lng' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 8',
            ],
            'address_line_1' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'address_line_2' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => 'null'
            ],
            'approval_status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1:Pending,2:Active,3:Rejected'
            ],
            'hidden' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'price_per_day' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'monthly_discount' => [
                'type' => 'INT',
                'unsigned' => true,
                'constraint' => 11,
                'default' => 0,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('offices');
    }

    public function down()
    {
        $this->forge->dropTable('offices');
    }
}
