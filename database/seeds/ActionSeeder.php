<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
          ['id' => 1, 'name' => 'UpdateProfile'],
          ['id' => 2, 'name' => 'UploadPhoto'],
        );
		
        foreach ($rows as $row)
        {
            $row['created_at'] = date("Y-m-d H:i:s");
            $row['updated_at'] = date("Y-m-d H:i:s");
            DB::table('actions')->insert($row);
        }
    }
}
