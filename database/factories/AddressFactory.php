<?php

use Faker\Generator as Faker;

use App\Address;
use App\Country;

$factory->define(App\Address::class, function (Faker $faker) {
    return [
        'street_name' => $faker->streetName,
        'street_number' => $faker->buildingNumber,
        'postal_code' => $faker->postcode,
        'postal_city' => $faker->city,
        'province' => $faker->country,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'country_id' => Country::first()
    ];
});
