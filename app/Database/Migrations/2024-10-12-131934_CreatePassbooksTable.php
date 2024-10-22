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
                'type' => 'BIGINT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'account_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'association_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'pbnum' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => false,
            ],
            'acnum' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => false,
            ],
            'assoc_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => false,
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
        $this->forge->addForeignKey('account_id', 'accounts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('association_id', 'associations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('orgid', 'organizations', 'orgid', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['creator', 'owner', 'pbnum', 'assoc_code']);
    }

    public function down()
    {
        $this->forge->dropTable('passbooks');
    }
}
