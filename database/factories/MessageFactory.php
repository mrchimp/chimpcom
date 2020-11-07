<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Message;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'message' => $this->faker->sentence,
            'recipient_id' => 1,
            'author_id' => 1,
            'has_been_read' => 0,
        ];
    }

    /**
     * Indicate that the message is read.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'has_been_read' => 1,
            ];
        });
    }
}
