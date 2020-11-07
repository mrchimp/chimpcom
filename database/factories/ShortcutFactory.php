<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Shortcut;

class ShortcutFactory extends Factory
{
    protected $model = Shortcut::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'url' => $this->faker->url,
        ];
    }
}
