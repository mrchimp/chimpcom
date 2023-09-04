<?php

namespace Tests\Feature\Commands;

use App\Mrchimp\Chimpcom\Id;
use App\User;
use Tests\TestCase;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Models\Project;

class TaskTagTest extends TestCase
{
    protected $other_user;

    protected $active_project;

    protected $first_task;

    protected $second_task;

    protected function makeTestTasks()
    {
        $this->user = User::factory()->create();

        // Create a bunch of extra tasks to force the tasks we're interest in
        // to be numbered in hex, not decimal.
        Task::factory()->count(32)->create([
            'user_id' => 999,
            'project_id' => 999,
        ]);

        $this->active_project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        $this->first_task = Task::factory()->create([
            'description' => 'First task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
            'priority' => 1,
        ]);

        $this->second_task = Task::factory()->create([
            'description' => 'Second task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
            'priority' => 1,
        ]);
    }

    /** @test */
    public function task_fails_for_guests()
    {
        $this->getGuestResponse('task:tag')
            ->assertStatus(404)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function can_add_or_remove_tag_to_a_task()
    {
        $this->makeTestTasks();
        $this->getUserResponse('task:tag ' . Id::encode($this->first_task->id) . ' ' . Id::encode($this->second_task->id) . ' @foo @bar')
            ->assertOk();

        $this->getUserResponse("task first")
            ->assertOk()
            ->assertSee("first")
            ->assertDontSee('second')
            ->assertSee("@foo")
            ->assertSee("@bar");
        $this->getUserResponse("task second")
            ->assertOk()
            ->assertSee('second')
            ->assertDontSee('first')
            ->assertSee("@foo")
            ->assertSee("@bar");

        $this->getUserResponse('task:tag --remove ' . Id::encode($this->first_task->id) . ' ' . Id::encode($this->second_task->id) . ' @foo @bar')
            ->assertOk();

        $this->getUserResponse("task first")
            ->assertOk()
            ->assertSee("first")
            ->assertDontSee("second")
            ->assertDontSee("@foo")
            ->assertDontSee("@bar");

        $this->getUserResponse("task second")
            ->assertOk()
            ->assertSee("second")
            ->assertDontSee("first")
            ->assertDontSee("@foo")
            ->assertDontSee("@bar");
    }
}
