<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
          ['id' => 1, 'name' => 'Hack Air Newbie', 'points_from' => 0, 'points_to' => 99],
          ['id' => 2, 'name' => 'Hack Air Enthusiast', 'points_from' => 100, 'points_to' => 499],
          ['id' => 3, 'name' => 'Hack Air Serious fighter', 'points_from' => 500, 'points_to' => 699],
          ['id' => 4, 'name' => 'Hack Air Police officer', 'points_from' => 700, 'points_to' => 999],
          ['id' => 5, 'name' => 'Hack Air Agent', 'points_from' => 1000, 'points_to' => 1499],
          ['id' => 6, 'name' => 'Hack Air Prodigy', 'points_from' => 1500, 'points_to' => 3999],
          ['id' => 7, 'name' => 'Hack Air Mastermind', 'points_from' => 4000, 'points_to' => 8999],
          ['id' => 8, 'name' => 'Hack Air Elite', 'points_from' => 9000, 'points_to' => 14999],
          ['id' => 9, 'name' => 'Hack Air Inspector', 'points_from' => 15000, 'points_to' => 24999],
          ['id' => 10, 'name' => 'Hack Air Elite Hacker', 'points_from' => 25000, 'points_to' => 49999],
          ['id' => 11, 'name' => 'Hack Air Hero', 'points_from' => 50000, 'points_to' => 99999]
        );
		
        foreach ($rows as $row)
        {
            $row['created_at'] = date("Y-m-d H:i:s");
            $row['updated_at'] = date("Y-m-d H:i:s");
            DB::table('levels')->insert($row);
        }
    }
}
