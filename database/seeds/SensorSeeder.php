<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
          ['id' => 1, 'name' => 'Draxis Test Sensor', 'user_id' => 1, 'access_key' => '123456', 'type' => 'arduino', 'location' => '{"type":"Feature","properties":{"label":"Themistokli Sofouli 54, Thessaloniki, Greece","label_short":"Thessaloniki, Greece"},"geometry":{"type":"Point","coordinates":[22.9497778,40.5928929]}}']
        );
		
        foreach ($rows as $row)
        {
            $row['created_at'] = date("Y-m-d H:i:s");
            $row['updated_at'] = date("Y-m-d H:i:s");
            DB::table('sensors')->insert($row);
        }
    }
}
