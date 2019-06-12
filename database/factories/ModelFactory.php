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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'telegram_id'   => $faker->unique()->randomDigit,
        'username'      => $faker->userName,
        'first_name'    => $faker->firstName,
        'lang'          => $faker->languageCode,
        'created_at'    => $faker->dateTimeBetween('-3 months', '-31 days')->format("Y-m-d H:i:s"),
        'updated_at'    => $faker->dateTimeBetween('-30 days')->format("Y-m-d H:i:s"),
    ];
});

$factory->define(App\Models\UserLocation::class, function (Faker\Generator $faker) {
    return [
        'user_id'       => factory(App\Models\User::class)->create()->id,
        'latitude'      => $faker->latitude,
        'longitude'     => $faker->longitude,
        'created_at'    => $faker->dateTimeBetween('-10 days'),
    ];
});

$factory->define(App\Models\Church::class, function (Faker\Generator $faker) {
   return [
       'city_id'    => \App\Models\City::select('id')->inRandomOrder()->take(1)->first()->id,
       'name'       => $faker->colorName,
       'address'    => $faker->address,
       'latitude'   => $faker->latitude,
       'longitude'  => $faker->longitude,
   ];
});
