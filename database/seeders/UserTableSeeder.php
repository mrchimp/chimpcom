<?php

namespace Database\Seeders;

use App\User as User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        User::create([
            'email' => 'admin@deviouschimp.co.uk',
            'password' => Hash::make('password'),
            'name' => 'root',
            'is_admin' => true,
        ]);

        User::create([
            'email' => 'guest@deviouschimp.co.uk',
            'password' => Hash::make('password'),
            'name' => 'guest',
            'is_admin' => false,
        ]);

        Model::reguard();
    }
}
