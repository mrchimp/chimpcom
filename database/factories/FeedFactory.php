<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Mrchimp\Chimpcom\Models\Feed;

$factory->define(Feed::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'name' => $faker->name,
        'url' => $faker->url,
    ];
});
