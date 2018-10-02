<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
          ['id' => 1, 'name' => 'Complete profile', 'description' => 'In your profile settings, enter as many infomation as possible.', 'description_short' => 'Enter your profile details', 'display_picture' => '', 'motivation' => 'Providing more information about you will allow us to provide personalized recommendations to you based on your profile!', 'cta_text' => 'Go to my profile', 'feedback_success' => 'Well done! You have updated your profile details!', 'action_id' => 1, 'points' => 100, 'visible_web' => true, 'visible_mobile' => true, 'type' => 'CompleteProfile'],
          ['id' => 2, 'name' => 'Keeping it in check', 'description' => 'Upload at least a photo of the sky from any location you want.', 'description_short' => 'Upload photo from location.', 'motivation' => 'Build the habit of watching the sky - your data help others.', 'display_picture' => '', 'cta_text' => 'Upload photo', 'feedback_success' => 'Thanks to your photo, we all get improved AQ data!', 'action_id' => 2, 'points' => 200, 'visible_web' => true, 'visible_mobile' => true, 'type' => 'UploadPhoto']        
        );
		
        foreach ($rows as $row)
        {
            $row['created_at'] = date("Y-m-d H:i:s");
            $row['updated_at'] = date("Y-m-d H:i:s");
            DB::table('missions')->insert($row);
        }
    }
}
