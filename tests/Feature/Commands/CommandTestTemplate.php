<?php

namespace Tests\Feature\Commands;

use App\User;
use Faker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CommandTestTemplate extends TestCase
{
    use DatabaseMigrations;

    protected $response_path = 'ajax/respond/json';
    protected $user;
    protected $admin;

    public function __construct()
    {
        parent::__construct();

        $this->faker = Faker\Factory::create();
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
            $this->user = factory(User::class)->create();
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
            $this->admin = factory(User::class)->states('admin')->create();
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
