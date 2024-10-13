<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateAssociationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'assoc_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],
            'community_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'office_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'creator' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'orgid' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
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
        $this->forge->addForeignKey('community_id', 'communities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('office_id', 'offices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('orgid', 'organizations', 'orgid', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['creator','owner', 'assoc_code']);
        $this->forge->createTable('associations');
    }

    public function down()
    {
        $this->forge->dropTable('associations');
    }
}
