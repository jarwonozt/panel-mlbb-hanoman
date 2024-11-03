<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePanelsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'panel_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'panel_url' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'admin_panel' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type'       => 'TEXT',
                'default'   => null,
            ],
            'expired' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,  // 0 = inactive, 1 = active
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('panels');
    }

    public function down()
    {
        $this->forge->dropTable('panels');
    }
}
