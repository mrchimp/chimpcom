<?php

namespace Tests;

use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    protected $response_path = 'ajax/respond/json';
    protected $user;
    protected $admin;

    public function __construct()
    {
        parent::__construct();

        $this->faker = Factory::create();
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

    protected function getAdminResponse($cmd_in, $user = null)
    {
        if (!$this->admin && is_null($user)) {
            $this->admin = factory(User::class)->states('admin')->create();
            $this->admin->save();
        }

        return $this
            ->actingAs($user ? $user : $this->admin)
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
