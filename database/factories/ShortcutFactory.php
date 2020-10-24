<?php

use Mrchimp\Chimpcom\Models\Shortcut;

$factory->define(Shortcut::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'url' => $faker->url,
    ];
});
