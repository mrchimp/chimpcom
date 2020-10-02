<?php

use Mrchimp\Chimpcom\Models\Project;

$factory->define(Project::class, function (Faker\Generator $faker) {
    return [
        'is_new' => 0,
        'name' => $faker->word,
        'description' => $faker->sentence,
        'user_id' => 1,
    ];
});
