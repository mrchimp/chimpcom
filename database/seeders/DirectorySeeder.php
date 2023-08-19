<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Mrchimp\Chimpcom\Models\Directory;

class DirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $root = Directory::factory()->create([
            'name' => '/'
        ]);
        $home = Directory::factory()->create([
            'name' => 'home',
        ]);

        $root->appendNode($home);

        foreach (User::all() as $user) {
            $user_home = Directory::factory()->create([
                'name' => e($user->name),
                'owner_id' => $user->id,
            ]);
            $home->appendNode($user_home);
        }
    }
}
