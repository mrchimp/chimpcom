<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    use WithoutModelEvents;

    private $sources = [
        'https://raw.githubusercontent.com/kkrypt0nn/wordlists/refs/heads/main/wordlists/languages/english.txt',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->sources as $source) {
            $text = file_get_contents($source);
            foreach (explode("\n", $text) as $word) {
                DB::table('words')->insert(['word' => $word]);
                echo 'Word: ' . $word . "\n";
            }
        }
    }
}
