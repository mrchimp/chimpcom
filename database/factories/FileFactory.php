<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Mrchimp\Chimpcom\Models\File;

$factory->define(File::class, function (Faker\Generator $faker) {
    return [
        'directory_id' => 1,
        'owner_id' => 1,
        'name' => $faker->word,
        'content' => $faker->sentence,
    ];
});
