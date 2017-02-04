<?php

namespace Tests\Commands;

use Faker;
use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommandTestTemplate extends TestCase
{
    use DatabaseTransactions;

    protected $response_path = 'ajax/respond/json';

    protected $user;
    protected $admin;

    public function __construct()
    {
        parent::__construct();

        $this->faker = $faker = Faker\Factory::create();
    }

    protected function getGuestResponse($cmd_in)
    {
        return $this->post($this->response_path, [
            'cmd_in' => $cmd_in
        ]);
    }

    protected function getUserResponse($cmd_in)
    {
        $this->user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => bcrypt($this->faker->password),
            'is_admin' => false,
        ]);

        return $this
            ->actingAs($this->user)
            ->post($this->response_path, [
                'cmd_in' => $cmd_in
            ]);
    }

    protected function getAdminResponse($cmd_in)
    {
        $this->admin = new User([
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => bcrypt($this->faker->password),
        ]);
        $this->admin->is_admin = true;
        $this->admin->save();

        return $this
            ->actingAs($this->admin)
            ->post($this->response_path, [
                'cmd_in' => $cmd_in
            ]);
    }
}
