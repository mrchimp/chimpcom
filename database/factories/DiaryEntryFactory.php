<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mrchimp\Chimpcom\Models\DiaryEntry;

class DiaryEntryFactory extends Factory
{
    protected $model = DiaryEntry::class;

    public function definition()
    {
        return [
            'content' => $this->faker->paragraph,
            'user_id' => 1,
            'date' => now(),
            'meta' => [
                'migraine' => $this->faker->numberBetween(0, 10),
                'word' => $this->faker->word,
            ],
        ];
    }
}
