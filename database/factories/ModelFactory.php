<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'surname' => $faker->lastName,
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => Hash::make('1234'),
        'active' => 1,
        'activated' => true,
        'private' => random_int(0, 1),
        'affiliate_id' => str_random(10),
        'referred_by' => function () {
            // 25% "chance" to have a referrer
            if (random_int(1, 4) == 1) {
                return factory(App\User::class)->create()->affiliate_id;
            } else {
                return null;
            }
        },
    ];
});

$factory->define(App\ForumTag::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word
    ];
});

$factory->define(App\ForumThread::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence(rand(3, 6), true),
        // this should be overridden when called from UsersSeeder
        'author_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'body' => $faker->paragraph
    ];
});

$factory->define(App\ForumReply::class, function ($faker) {
    return [
        'body' => $faker->paragraph,
        // these two should be overridden when called from UsersSeeder
        'forum_thread_id' => function () {
            return factory(App\ForumThread::class)->create()->id;
        },
        'author_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});
