<?php

use Carbon\Carbon;
use Mrchimp\Chimpcom\Models\Task;

$factory->define(Task::class, function (Faker\Generator $faker) {
    return [
        'completed' => 0,
        'project_id' => 1,
        'priority' => 1,
        'description' => $faker->sentence(5),
        'user_id' => 1,
    ];
});

$factory->state(Task::class, 'completed', function (Faker\Generator $faker) {
    return [
        'completed' => 1,
        'time_completed' => Carbon::now(),
    ];
});

$factory->state(Task::class, 'highpriority', function (Faker\Generator $faker) {
    return [
        'priority' => 10,
    ];
});
