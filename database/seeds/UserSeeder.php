<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 10 users using model factory
        factory(App\User::class, 10)->create();

        // create 5 users with forum activity
        // factory(App\User::class, 5)->create()->each(function ($user) {
        //         // generate zero to four threads for each user
        //         factory(\App\ForumThread::class, rand(1, 4))->create([
        //             'author_id' => $user->id,
        //         ])->each(function ($thread) use ($user) {
        //             // create one or two tags for each thread
        //             for ($i = rand(0, 1); $i < 2; $i++) {
        //                 $thread->tags()->save(factory(App\ForumTag::class)->create());
        //             };
        //             // generate zero to 20 replies on each thread
        //             factory(\App\ForumReply::class, rand(0, 20))->create([
        //                 'author_id' => $user->id,
        //                 'forum_thread_id' => $thread->id,
        //             ]);
        //         });
        //     });

    }
}
