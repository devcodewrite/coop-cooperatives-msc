<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateOfficesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'off_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'region_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'district_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'orgid' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'owner' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'creator' => [
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
        $this->forge->addForeignKey('region_id', 'regions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('district_id', 'districts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('orgid', 'organizations', 'orgid', 'CASCADE', 'CASCADE');
        $this->forge->createTable('offices');
    }

    public function down()
    {
        $this->forge->dropTable('offices');
    }
}
