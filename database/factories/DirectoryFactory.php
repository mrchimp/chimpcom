<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Directory;

class DirectoryFactory extends Factory
{
    protected $model = Directory::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
