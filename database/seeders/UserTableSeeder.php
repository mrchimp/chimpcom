<?php

namespace Database\Seeders;

use App\User as User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

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
            'name' => 'root'
        ]);

        Model::reguard();
    }
}
