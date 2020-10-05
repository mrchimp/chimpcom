<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Mrchimp\Chimpcom\Models\Directory;

$factory->define(Directory::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});
