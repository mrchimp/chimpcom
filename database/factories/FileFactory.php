<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\File;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition()
    {
        return [
            'directory_id' => 1,
            'owner_id' => 1,
            'name' => $this->faker->word,
            'content' => $this->faker->sentence,
        ];
    }
}
