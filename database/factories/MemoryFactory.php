<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\Memory;

class MemoryFactory extends Factory
{
    protected $model = Memory::class;

    public function definition()
    {
        return [
            'name' => 'foo',
            'content' => 'bar',
            'user_id' => 1,
            'public' => 0,
        ];
    }

    /**
     * Indicate that the memory is public.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function public()
    {
        return $this->state(function (array $attributes) {
            return [
                'public' => 1,
            ];
        });
    }
}
