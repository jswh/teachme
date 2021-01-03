<?php

use Faker\Generator as Faker;

$factory->define(App\Models\School::class, function (Faker $faker) {
    return [
            'name' => $faker->name,
            'description' => 'description',
            'creator_id' => 1,
            'status' => 1
        ];
});
