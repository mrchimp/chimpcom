<?php

namespace Tests;

use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mrchimp\Chimpcom\Models\Directory;

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

    protected function getUserEditSaveResponse($content, $user = null, $cmd_in = '')
    {
        if (!$this->user && is_null($user)) {
            $this->user = factory(User::class)->create();
        }

        return $this
            ->actingAs($user ? $user : $this->user)
            ->post(
                $this->response_path,
                [
                    'content' => $content,
                    'cmd_in' => $cmd_in,
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

    /**
     * Create a newsted oparth from a string
     *
     * @param string $path e.g. /home/mrchimp/blog
     * @return void
     */
    protected function createPath(string $path): Directory
    {
        $parts = explode('/', $path);

        $dir = null;

        foreach ($parts as $part) {
            if (empty($part)) {
                continue;
            }

            $new = factory(Directory::class)->create([
                'name' => $part,
            ]);

            if ($dir) {
                $dir->appendNode($new);
            }

            $dir = $new;
        }

        return $dir;
    }
}
