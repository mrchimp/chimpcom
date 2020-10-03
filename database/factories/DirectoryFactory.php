<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Mrchimp\Chimpcom\Models\Directory;

$factory->define(Directory::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
