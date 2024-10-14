<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateDistrictsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],
            'capital' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['Municipal', 'District'],
            ],
            'region_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'on_update' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('region_id', 'regions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['category']);
        $this->forge->createTable('districts');
    }

    public function down()
    {
        $this->forge->dropTable('districts');
    }
}
