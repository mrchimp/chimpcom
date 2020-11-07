<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Project;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'is_new' => 0,
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'user_id' => 1,
        ];
    }
}
