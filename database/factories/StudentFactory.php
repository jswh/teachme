<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Student::class, function (Faker $faker) {
    return [
            'name' => $faker->name,
            'usename' => $faker->unique()->userName,
            'password' => $faker->password,
            'school_id' => 1,
        ];
});
