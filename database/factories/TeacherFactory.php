<?php

use Faker\Generator as Faker;
//principal
$factory->define(App\Models\Teacher::class, function (Faker $faker) {
    return [
        'id' => 1,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'school_id' => null,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
    ];
});
