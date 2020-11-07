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
        $root = factory(Directory::class)->create([
            'name' => '/'
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
        ]);

        $root->appendNode($home);

        foreach (User::all() as $user) {
            $user_home = factory(Directory::class)->create([
                'name' => e($user->name),
                'owner_id' => $user->id,
            ]);
            $home->appendNode($user_home);
        }
    }
}
