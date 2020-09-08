<?php

namespace Tests\Commands;

use App\User;
use Faker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
        return $this->post(
            $this->response_path,
            [
                'cmd_in' => $cmd_in
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]
        );
    }

    protected function getUserResponse($cmd_in, $user = null)
    {
        if (!$this->user && is_null($user)) {
            $this->user = User::create([
                'name' => $this->faker->name,
                'email' => $this->faker->safeEmail,
                'password' => bcrypt($this->faker->password),
                'is_admin' => false,
            ]);
        }

        return $this
            ->actingAs($user ? $user : $this->user)
            ->post(
                $this->response_path,
                [
                    'cmd_in' => $cmd_in
                ],
                [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ]
            );
    }

    protected function getAdminResponse($cmd_in)
    {
        if (!$this->admin) {
            $this->admin = new User([
                'name' => $this->faker->name,
                'email' => $this->faker->safeEmail,
                'password' => bcrypt($this->faker->password),
            ]);
            $this->admin->is_admin = true;
            $this->admin->save();
        }

        return $this
            ->actingAs($this->admin)
            ->post(
                $this->response_path,
                [
                    'cmd_in' => $cmd_in
                ],
                [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ]
            );
    }
}
