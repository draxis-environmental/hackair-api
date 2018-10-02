<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OutdoorActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
          ['id' => 1, 'name' => 'Running', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 2, 'name' => 'Walking', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 3, 'name' => 'Picnic', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 4, 'name' => 'Outdoor job', 'created_at' => date("Y-m-d H:i:s")],
          ['id' => 5, 'name' => 'Walking with a baby', 'created_at' => date("Y-m-d H:i:s")]
        );

        foreach ($rows as $row)
        {
            DB::table('outdoor_activities')->insert($row);
        }
    }
}
