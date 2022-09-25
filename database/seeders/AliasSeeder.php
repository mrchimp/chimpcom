<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mrchimp\Chimpcom\Models\Alias;

class AliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Alias::create([
            'name' => 'man',
            'alias' => 'help',
        ]);
    }
}
