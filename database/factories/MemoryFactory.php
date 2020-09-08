<?php

use Mrchimp\Chimpcom\Models\Memory;

$factory->define(Memory::class, function (Faker\Generator $faker) {
    return [
        'name' => 'foo',
        'content' => 'bar',
        'user_id' => 1,
        'public' => 0,
    ];
});

$factory->state(Memory::class, 'public', function (Faker\Generator $faker) {
    return [
        'public' => 1,
    ];
});
