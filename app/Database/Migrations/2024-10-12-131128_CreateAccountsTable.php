<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateAccountsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'acnum' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => false,
            ],
            'title' => [
                'type' => 'ENUM',
                'constraint' => ['mr', 'mrs', 'miss', 'dr', 'prof'],
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => false,
            ],
            'given_name' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false,
            ],
            'family_name' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false,
            ],
            'sex' => [
                'type' => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'null' => false,
            ],
            'dateofbirth' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'occupation' => [
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true,
            ],
            'primary_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => true,
            ],
            'marital_status' => [
                'type' => 'ENUM',
                'constraint' => ['single', 'married', 'divorced', 'widowed'],
                'null' => true,
            ],
            'education' => [
                'type' => 'ENUM',
                'constraint' => ['none', 'primary', 'secondary', 'tertiary', 'postgraduate', 'other'],
                'null' => true,
            ],
            'nid_type' => [
                'type' => 'ENUM',
                'constraint' => ['passport', 'driver_license', 'voter_id', 'national_id_card'],
                'null' => true,
            ],
            'nid' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'photo' => [
                'type' => 'TINYTEXT',
                'null' => true,
            ],
            'community_id' => [
                'type' => 'INT',
                'unsigned' => true,
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
        $this->forge->createTable('accounts');
        $this->forge->addForeignKey('community_id', 'communities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('orgid', 'organizations', 'orgid', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['creator','owner','acnum']);
    }

    public function down()
    {
        $this->forge->dropTable('accounts');
    }
}
