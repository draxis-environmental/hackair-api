<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
          ['id' => 1, 'name' => 'Feels like home!', 'description' => 'Spend 1 hour in the app.', 'motivation' => 'The longer you review, study or upload data, the better final outcome is.', 'display_picture' => 'BadgeBG_FeelsL_like_home.png'],
          ['id' => 2, 'name' => 'Hackathon', 'description' => 'You have logged three days in a row!', 'motivation' => 'By checking out AQ on a regular basis, you receive information that helps you to make better decisions and plan ahead.', 'display_picture' => 'BadgeBG_Hackathon.png'],
          ['id' => 3, 'name' => 'A health watcher!', 'description' => 'In profile settings, mark what type of health group you are in.', 'motivation' => 'More concrete data allows us to work on more elaborate strategies and public calls.', 'display_picture' => 'BadgeBG_Health_watcher.png', 'type' => 'SelectUserGroups', 'action_id' => 1],
          ['id' => 4, 'name' => 'Fresh air hunter', 'description' => 'Visit locations with low index of air pollution.', 'motivation' => 'More time spent in these area means healthier life for you.', 'display_picture' => 'BadgeBG_Fresh_air_hunter.png'],
          ['id' => 5, 'name' => 'Keep it scientific.', 'description' => 'Review and share data from sensor measurements.', 'motivation' => 'Don’t count just on your feelings. Make your decisions built on rock solid base.', 'display_picture' => 'BadgeBG_Keep_it_scientific.png'],
          ['id' => 6, 'name' => 'Share with others', 'description' => 'Post a question or a reply on our community forum in web app.', 'motivation' => 'When people collaborate, it creates 2+2 equals 5 effect.', 'display_picture' => 'BadgeBG_Share_with_others.png'],
          ['id' => 7, 'name' => 'Watcher on the wall!', 'description' => 'Upload 5 different photos.', 'motivation' => 'More data means more accurate feedback for us.', 'display_picture' => 'BadgeBG_Watcher_on_the_wall.png'],
          ['id' => 8, 'name' => 'HackAIR\'s hero', 'description' => 'Install at least 3 sensors.', 'motivation' => 'More data means more accurate measurement.', 'display_picture' => 'BadgeBG_HackAir\'s_hero.png'],
          ['id' => 9, 'name' => 'Prolific hacker', 'description' => 'Measure and upload at least 1 photo today.', 'motivation' => 'The more data we collect, the stronger our claims and public calls may be.', 'display_picture' => 'BadgeBG_Prolific_hacker.png'],
          ['id' => 10, 'name' => 'A survivor', 'description' => 'Take a photo / measurement at the same place at least 3 days in a row. If all measurements show bad AQ, you will receive this badge.', 'motivation' => 'The odds may be against you for now, but still, you want to fight for a better future.', 'display_picture' => 'BadgeBG_Survivor.png'],
          ['id' => 11, 'name' => 'A hacker\'s masterpiece', 'description' => 'Install at least 1 sensor. ', 'motivation' => 'Even one sensor may help to get realistic bigger picture.', 'display_picture' => 'BadgeBG_Hacker\'s_masterpiece.png'],
          ['id' => 12, 'name' => 'Constantly on the move', 'description' => 'Move sensor to a different area.', 'motivation' => '', 'display_picture' => 'BadgeBG_Constantly_on_the_move.png'],
          ['id' => 13, 'name' => 'Beacon in the dark', 'description' => 'Share your data and findings with others.', 'motivation' => 'By sharing, you raise awareness and help to spread the word!', 'display_picture' => 'BadgeBG_Beacon_in_the_dark.png'],
          ['id' => 14, 'name' => 'How is your life today?', 'description' => 'Submit how you feel today on our barometer.', 'motivation' => 'Your subjective perception is as important as objective measurements.', 'display_picture' => 'BadgeBG_How_is_your_life_today.png'],
          ['id' => 15, 'name' => 'History buff', 'description' => 'Check historical data going back to 10 days.', 'motivation' => 'Seeing the bigger picture allows you to make smart decisions.', 'display_picture' => 'BadgeBG_History_buff.png'],
          ['id' => 16, 'name' => 'Helping hand', 'description' => 'Invite a friend so you can hack the air together.', 'motivation' => 'The bigger our community is, the bigger impact we may produce.', 'display_picture' => 'BadgeBG_Helping_hand.png'],
          ['id' => 17, 'name' => 'Hacker nomad', 'description' => 'Upload photos from at least 5 different locations.', 'motivation' => 'Taking measurements in different areas helps us compare data more precisely.', 'display_picture' => 'BadgeBG_Hacker_nomad.png']
        );
		
        foreach ($rows as $row)
        {
            $row['created_at'] = date("Y-m-d H:i:s");
            $row['updated_at'] = date("Y-m-d H:i:s");
            DB::table('achievements')->insert($row);
        }
    }
}
