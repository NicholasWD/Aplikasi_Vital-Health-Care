<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIconToVitalCategories extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vital_categories', [
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'default' => '📊',
                'after' => 'unit',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('vital_categories', 'icon');
    }
}
