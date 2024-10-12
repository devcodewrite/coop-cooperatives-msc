<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateOrganizationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'orgid' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'name' => [
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

        $this->forge->addKey('orgid', true);
        $this->forge->createTable('organizations');
    }

    public function down()
    {
        $this->forge->dropTable('organizations');
    }
}
