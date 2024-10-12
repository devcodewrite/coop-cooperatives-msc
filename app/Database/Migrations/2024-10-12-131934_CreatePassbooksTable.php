<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePassbooksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'pbnum' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'acnum' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'assoc_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'orgid' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'creator' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => false,
            ],
            'owner' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => false,
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
        $this->forge->createTable('passbooks');
    }

    public function down()
    {
        $this->forge->dropTable('passbooks');
    }
}
