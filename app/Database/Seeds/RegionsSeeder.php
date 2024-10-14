<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegionsSeeder extends Seeder
{
    public function run()
    {
        $data = array(
            array('id' => '1','name' => 'Ashanti Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '2','name' => 'Ahafo Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '3','name' => 'Central Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '4','name' => 'Eastern Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '5','name' => 'Greater Accra Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '6','name' => 'Northern Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '7','name' => 'Upper East Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '8','name' => 'Upper West Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '9','name' => 'Volta Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '10','name' => 'Western Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '11','name' => 'Western North Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '12','name' => 'Oti Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '13','name' => 'Bono Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '14','name' => 'Bono East Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '15','name' => 'North East Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46'),
            array('id' => '16','name' => 'Savannah Region','short_name' => '','deleted_at' => NULL,'updated_at' => '2024-10-14 09:52:38','created_at' => '2023-08-07 19:08:46')
          );          

        // Using Query Builder to insert data
        $this->db->table('regions')->insertBatch($data);
    }
}
