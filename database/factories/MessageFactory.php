<?php

use Mrchimp\Chimpcom\Models\Message;

$factory->define(Message::class, function (Faker\Generator $faker) {
    return [
        'message' => 'Here is some mesasge text.',
        'recipient_id' => 1,
        'author_id' => 1,
        'has_been_read' => 0,
    ];
});

$factory->state(Message::class, 'read', function (Faker\Generator $faker) {
    return [
        'has_been_read' => 1,
    ];
});
