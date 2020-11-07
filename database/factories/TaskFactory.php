<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Task;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'completed' => 0,
            'project_id' => 1,
            'priority' => 1,
            'description' => $this->faker->sentence(5),
            'user_id' => 1,
        ];
    }

    /**
     * Indicate that the task is completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'completed' => 1,
                'time_completed' => Carbon::now(),
            ];
        });
    }

    /**
     * Indicate that the task is high priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function highpriority()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 10,
            ];
        });
    }
}
