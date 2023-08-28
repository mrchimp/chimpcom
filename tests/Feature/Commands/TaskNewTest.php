<?php

namespace Tests\Feature\Commands;

use App\User;
use Tests\TestCase;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Models\Project;

class TaskNewTest extends TestCase
{
    protected $other_user;
    protected $active_project;
    protected $other_project;
    protected $other_users_project;

    /** @test */
    public function task_fails_for_guests()
    {
        $this->getGuestResponse('task:new')
            ->assertStatus(404)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    protected function makeTestTasks()
    {
        $this->user = User::factory()->create();
        $this->other_user = User::factory()->create();

        $this->active_project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->other_project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->other_users_project = Project::factory()->create([
            'user_id' => $this->other_user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        $this->other_user->active_project_id = $this->other_users_project->id;
        $this->other_user->save();

        Task::factory()->create([
            'description' => 'Low priority task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
            'priority' => 1,
        ]);

        Task::factory()->highpriority()->create([
            'description' => 'High priority task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
            'priority' => 10,
        ]);

        Task::factory()->completed()->create([
            'description' => 'Completed task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
        ]);

        Task::factory()->create([
            'description' => 'Task on other project',
            'user_id' => $this->user->id,
            'project_id' => $this->other_project->id,
        ]);

        Task::factory()->create([
            'description' => 'Other users task',
            'user_id' => $this->other_user->id,
            'project_id' => $this->other_users_project->id,
        ]);
    }

    /** @test */
    public function task_new_fails_if_user_has_no_active_project()
    {
        $this->getUserResponse('task:new Do a thing')
            ->assertSee('No active project')
            ->assertStatus(200);
    }

    /** @test */
    public function task_new_can_create_a_new_task()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $user->setActiveProject($project);

        $this->getUserResponse('task:new Do a thing', $user)
            ->assertSee('Task created.')
            ->assertStatus(200);

        $task = Task::first();

        $this->assertEquals(1, Task::count());

        $this->assertEquals('Do a thing', $task->description);
        $this->assertEquals($user->id, $task->user_id);
        $this->assertEquals(0, $task->completed);
        $this->assertEquals(1, $task->priority);
    }

    /** @test */
    public function task_new_can_set_a_high_priority_on_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $user->setActiveProject($project);

        $this->getUserResponse('task:new Do an important thing --priority 10', $user)
            ->assertSee('Task created.')
            ->assertStatus(200);

        $this->assertEquals(10, Task::first()->priority);
    }
}
