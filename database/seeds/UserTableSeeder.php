<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User as User;

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
