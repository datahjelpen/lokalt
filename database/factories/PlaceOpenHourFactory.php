<?php

use Faker\Generator as Faker;
use App\Place;

$factory->define(App\PlaceOpenHour::class, function (Faker $faker) {
    if (rand(0,1)) {
        $weekday = $faker->numberBetween(0, 6);
        $time_from = $faker->time('H:i:s', '09:00');
        $time_to = $faker->time('H:i:s', '18:00');
        $special_hours_date = null;
    } else {
        $weekday = null;
        $time_from = $faker->time('H:i:s', '12:00');
        $time_to = $faker->time('H:i:s', '22:00');
        $special_hours_date = $faker->datetime;
    }

    return [
        'place_id' => Place::inRandomOrder()->first(),
        'weekday' => $weekday,
        'time_from' => $time_from,
        'time_to' => $time_to,
        'special_hours_date' => $special_hours_date,
    ];
});
