<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUploadsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'filename'   => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'filepath'   => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
                'null'       => false,
            ],
            'uploaded_at'=> [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('uploads', true);
    }

    public function down()
    {
        $this->forge->dropTable('uploads', true);
    }
}
