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

$factory->define(\App\Artvenue\Models\User::class, function (Faker\Generator $faker) {
    return [
        'username'       => $faker->name,
        'password'       => bcrypt('admin123'),
        'email'          => $faker->email,
        'confirmed_at'   => \Carbon\Carbon::now(),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\App\Artvenue\Models\Image::class, function (Faker\Generator $faker) {
    return [
        'user_id'     => 1,
        'image_name'  => '1.jpg',
        'title'       => $faker->name,
        'slug'        => str_slug($faker->name),
        'approved_at' => \Carbon\Carbon::now(),
        'category_id' => 1
    ];
});
