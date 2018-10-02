<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
          ['id' => 1, 'name' => 'Asthma', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 2, 'name' => 'Allergy', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 3, 'name' => 'Cardiovascular', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 4, 'name' => 'Circulatory', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 5, 'name' => 'General Health Problem', 'created_at' => date("Y-m-d H:i:s")]
        );

        foreach ($rows as $row)
        {
            DB::table('user_groups')->insert($row);
        }
    }
}
