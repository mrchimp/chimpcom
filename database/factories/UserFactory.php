<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => bcrypt($this->faker->password),
            'remember_token' => Str::random(10),
            'active_project_id' => 0,
            'is_admin' => 0,
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => 1,
            ];
        });
    }
}
