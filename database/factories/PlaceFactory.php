<?php

use Faker\Generator as Faker;

use App\Address;

$factory->define(App\Place::class, function (Faker $faker) {
    // Save name so we can create a slug of the same name
    $name = $faker->unique()->company;

    // As you can see, we get a random value from arrays with a single value and two nulls.
    // This is because the fields are nullable, and users are less likely to fill out everything
    // Basically it just crates more realistic data
    return [
        'name' => $name,
        'slug' => str_slug($name),
        'description' => [$faker->paragraph, null, null][mt_rand(0, 2)],
        'address_id' => Address::inRandomOrder()->first(),
        'website' => [$faker->domainName, null, null][mt_rand(0, 2)],
        'phone' => [$faker->phoneNumber, null, null][mt_rand(0, 2)],
        'email' => [$faker->email, null, null][mt_rand(0, 2)],
        'founded_at' => [$faker->dateTime, null, null][mt_rand(0, 2)]
    ];
});
