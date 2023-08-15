<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\Migration;

class TokensTable extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'email'=>[
                'type' => 'VARCHAR',
                'constraint' => '100'
            ],
            'token' => [
                'type' => 'TEXT'
            ],
            'expiration_time'=>[
                'type' => 'VARCHAR',
                'constraint'=>'30'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],

        ]);

        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('tokens');
    }

    public function down()
    {
        $this->forge->dropTable('tokens');
    }
}
