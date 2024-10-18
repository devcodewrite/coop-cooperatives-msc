<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateCommunitiesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'com_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'office_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'region_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'district_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'creator' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'owner' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
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
        $this->forge->addForeignKey('office_id', 'offices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('region_id', 'regions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('district_id', 'districts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('orgid', 'organizations', 'orgid', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['creator','owner']);
        $this->forge->createTable('communities');
    }

    public function down()
    {
        $this->forge->dropTable('communities');
    }
}
