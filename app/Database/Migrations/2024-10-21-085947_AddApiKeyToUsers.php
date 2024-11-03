<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApiKeyToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'api_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,  // null until it's generated
                'unique'     => true,  // make sure the API key is unique
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'api_key');
    }
}
