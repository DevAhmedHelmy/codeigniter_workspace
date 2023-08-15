<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfficesTagsTable extends Migration
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
            'office_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tag_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ]
        ]);

        $this->forge->addForeignKey('office_id', 'offices', 'id');
        $this->forge->addForeignKey('tag_id', 'tags', 'id');
        $this->forge->addUniqueKey(['office_id', 'tag_id']);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('offices_tags');
    }

    public function down()
    {
        $this->forge->dropTable('offices_tags');
    }
}
