<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(SensorSeeder::class);
        $this->call(UserGroupSeeder::class);
        $this->call(OutdoorActivitySeeder::class);
        $this->call(LevelSeeder::class);
        $this->call(ActionSeeder::class);
        $this->call(AchievementSeeder::class);
        $this->call(MissionSeeder::class);
    }
}
