<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mrchimp\Chimpcom\Models\DiaryEntry;

class diary_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $num_entries = 60;

        for ($i = 0; $i < $num_entries; $i++) {
            $date = now()->subDays($i);

            DiaryEntry::factory()->create([
                'date' => $date,
            ]);
        }
    }
}
