<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Event;
use Mrchimp\Chimpcom\Models\Project;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'message' => $this->faker->sentence,
            'user_id' => 1,
            'date' => $this->faker->dateTimeBetween('now', '+90 days'),
            'project_id' => null,
        ];
    }

    public function project(Project $project)
    {
        return $this->state(function () use ($project) {
            return [
                'project_id' => $project->id,
            ];
        });
    }
}
