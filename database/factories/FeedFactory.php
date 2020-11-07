<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Feed;

class FeedFactory extends Factory
{
    protected $model = Feed::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'name' => $this->faker->name,
            'url' => $this->faker->url,
        ];
    }
}
