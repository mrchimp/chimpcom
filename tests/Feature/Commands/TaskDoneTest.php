<?php

namespace Tests\Feature\Commands;

use App\User;
use Tests\TestCase;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Models\Project;

class TaskDoneTest extends TestCase
{
    protected $other_user;

    protected $active_project;

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
    public function done_command_fails_if_user_has_no_active_project()
    {
        $this->getUserResponse('task done 1')
            ->assertStatus(200)
            ->assertSee('No active project');
    }

    /** @test */
    public function done_command_fails_if_task_cannot_be_found()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);
        $user->active_project_id = $project->id;
        $user->save();

        $this->getUserResponse('task done 1', $user)
            ->assertStatus(200)
            ->assertSee('Couldn\'t find that task.');
    }

    /** @test */
    public function done_command_cues_up_the_done_action_if_all_is_well()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);
        $user->active_project_id = $project->id;
        $user->save();
        Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->getUserResponse('task done 1', $user)
            ->assertStatus(200)
            ->assertSee('Are you sure you want to mark this as complete?');

        $this->assertAction('done');
    }

    /** @test */
    public function done_command_force_option_skips_confirmation()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);
        $user->active_project_id = $project->id;
        $user->save();
        Task::factory()->count(2)->create([
            'project_id' => $project->id,
        ]);

        $this->getUserResponse('task done 1 --force', $user)
            ->assertStatus(200)
            ->assertSee('Ok')
            ->assertSessionMissing('action');

        $this->getUserResponse('task done 2 -f', $user)
            ->assertStatus(200)
            ->assertSee('Ok')
            ->assertSessionMissing('action');

        $this->assertEquals(0, Task::where('completed', 0)->count());
    }
}
