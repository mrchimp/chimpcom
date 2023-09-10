<?php

namespace Tests;

use App\User;
use Faker\Factory;
use Illuminate\Support\Arr;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Directory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mrchimp\Chimpcom\Models\Project;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    protected $response_path = 'ajax/respond/json';
    protected $user;
    protected $admin;
    protected $last_action_id;
    protected $faker;
    protected $active_project;

    public function __construct()
    {
        parent::__construct();

        $this->faker = Factory::create();
    }

    protected function getGuestResponse($cmd_in, $action_id = null)
    {
        $response = $this->post(
            $this->response_path,
            [
                'cmd_in' => $cmd_in,
                'action_id' => $action_id,
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]
        );

        $this->last_action_id = Arr::get($response->json(), 'action_id');

        return $response;
    }

    protected function makeTestUser()
    {
        $this->user = User::factory()->create();

        return $this->user;
    }

    protected function getUserResponse($cmd_in, $user = null, $action_id = null)
    {
        if (!$this->user && is_null($user)) {
            $this->makeTestUser();
        }

        $response = $this
            ->actingAs($user ? $user : $this->user)
            ->post(
                $this->response_path,
                [
                    'cmd_in' => $cmd_in,
                    'action_id' => $action_id,
                ],
                [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ]
            );

        $this->last_action_id = Arr::get($response->json(), 'action_id');

        return $response;
    }

    protected function getUserEditSaveResponse($content, $user = null, $cmd_in = '', $action_id = null)
    {
        if (!$this->user && is_null($user)) {
            $this->user = User::factory()->create();
        }

        $response = $this
            ->actingAs($user ? $user : $this->user)
            ->post(
                $this->response_path,
                [
                    'content' => $content,
                    'cmd_in' => $cmd_in,
                    'action_id' => $action_id,
                ],
                [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ]
            );

        $this->last_action_id = Arr::get($response->json(), 'action_id');

        return $response;
    }

    protected function getAdminResponse($cmd_in, $user = null, $action_id = null)
    {
        if (!$this->admin && is_null($user)) {
            $this->admin = User::factory()->admin()->create();
            $this->admin->save();
        }

        $response = $this
            ->actingAs($user ? $user : $this->admin)
            ->post(
                $this->response_path,
                [
                    'cmd_in' => $cmd_in,
                    'action_id' => $action_id,
                ],
                [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ]
            );

        $this->last_action_id = Arr::get($response->json(), 'action_id');

        return $response;
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

            $new = Directory::factory()->create([
                'name' => $part,
            ]);

            if ($dir) {
                $dir->appendNode($new);
            }

            $dir = $new;
        }

        return $dir;
    }

    protected function setAction(string $name, array $data = []): string
    {
        return Chimpcom::setAction($name, $data);
    }

    protected function assertNoAction(): void
    {
        $this->assertNull($this->last_action_id, 'Action ID was "' . $this->last_action_id . '" when it should be null');
    }

    protected function assertAction(string $action_name): void
    {
        $action = $this->getAction();

        $this->assertNotNull($action, 'Action was not found when looking for name ' . $action_name);
        $this->assertEquals($action_name, $action['action_name'], 'Action name "' . $action['action_name'] . '" did not match expected "' . $action_name . '"');
    }

    protected function assertActionData(array $data): void
    {
        $action = $this->getAction();

        $this->assertNotNull($action, 'Action was not found when looking for key ' . json_encode($data));

        foreach ($data as $key => $expected_value) {
            $this->assertEquals($expected_value, $data[$key], 'Action data "' . $key . '" did not match expected value "' . $expected_value . '"');
        }
    }

    protected function getAction()
    {
        if (!$this->last_action_id) {
            return null;
        }

        return Chimpcom::getAction($this->last_action_id);
    }

    protected function getLastActionId(): ?string
    {
        return $this->last_action_id;
    }

    protected function assertActionExists(string $action_id): void
    {
        $this->assertTrue(Chimpcom::actionExists($action_id), 'Action ID should have been "' . $action_id . '" but was did not exist');
    }

    protected function assertActionDoesntExist(string $action_id): void
    {
        $this->assertFalse(Chimpcom::actionExists($action_id), 'Action should not exist but action_id "' . $action_id . '" was found');
    }

    protected function createProject(User $user = null): Project
    {
        if (!$this->user && !$user) {
            $this->makeTestUser();
        }

        if ($user === null) {
            $user = $this->user;
        }

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->active_project_id = $project->id;
        $user->save();

        return $project;
    }
}
