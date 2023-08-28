<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\User;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\Task;
use App\Mrchimp\Chimpcom\Id;

class TaskEditTest extends TestCase
{
    use DatabaseMigrations;

    protected $other_user;
    protected $active_project;
    protected $other_project;
    protected $other_users_project;

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
    public function task_fails_for_guests()
    {
        $this->getGuestResponse('task:edit')
            ->assertStatus(404)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function can_edit_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);
        $user->active_project_id = $project->id;
        $user->save();
        $task = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->getUserResponse('task:edit ' . Id::encode($task->id), $user)
            ->assertOk();

        $this->assertAction('edit_task');
        $this->assertActionData(['task_to_edit' => $task->id]);

        $this->getUserEditSaveResponse(
            'updated task content',
            $user,
            '',
            $this->last_action_id
        )
            ->assertOk();

        $task->refresh();

        $this->assertEquals('updated task content', $task->description);
    }

    /** @test */
    public function priority_must_be_an_integer()
    {
        $this->makeTestTasks();
        $this->task_fails_for_guests();
        $this->getUserResponse('task:edit 1 --priority not_a_number')
            ->assertSee('Priority must be an integer.')
            ->assertStatus(200);
    }

    /** @test */
    public function users_can_set_priority_on_tasks()
    {
        $this->makeTestTasks();
        $this->getUserResponse('task:edit 1 --priority 10')
            ->assertStatus(200)
            ->assertSee('Priority set to 10 for task:');
    }

    /** @test */
    public function cant_set_priority_on_a_task_that_doesnt_exist()
    {
        $this->makeTestTasks();
        $this->getUserResponse('task:edit 99999 --priority 10')
            ->assertSee('Could not find task.')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_set_priority_on_a_task_you_dont_own()
    {
        $this->makeTestTasks();
        $task = Task::factory()->create([
            'user_id' => 9999,
        ]);

        $this->getUserResponse('task:edit ' . $task->id . ' --priority 10')
            ->assertStatus(200)
            ->assertSee('Could not find task.');
    }
}
