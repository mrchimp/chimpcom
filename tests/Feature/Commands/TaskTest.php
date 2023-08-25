<?php

namespace Tests\Feature\Commands;

use App\User;
use Tests\TestCase;
use App\Mrchimp\Chimpcom\Id;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Models\Project;

class TaskTest extends TestCase
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
    public function task_fails_for_guests()
    {
        $this->getGuestResponse('task')
            ->assertStatus(404)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function task_returns_error_if_no_active_project()
    {
        $this->user = User::factory()->create();
        $this->user->active_project_id = 0;
        $this->user->save();

        $this->getUserResponse('task')
            ->assertStatus(200)
            ->assertSee('No active project.');
    }

    /** @test */
    public function by_default_task_shows_incomplete_tasks_on_current_project()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task')
            ->assertStatus(200)
            ->assertSee('Low priority task')
            ->assertSee('High priority task')
            ->assertDontSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function task_command_with_all_option_shows_completed_and_uncompleted_tasks()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task --all')
            ->assertStatus(200)
            ->assertSee('Low priority task')
            ->assertSee('High priority task')
            ->assertSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function task_command_with_allprojects_option_shows_tasks_from_all_users_projects()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task --allprojects')
            ->assertStatus(200)
            ->assertSee('Low priority task')
            ->assertSee('High priority task')
            ->assertDontSee('Completed task')
            ->assertSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function task_command_with_completed_option_shows_tasks_from_all_users_projects()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task --complete')
            ->assertStatus(200)
            ->assertDontSee('Low priority task')
            ->assertDontSee('High priority task')
            ->assertSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function task_command_num_tasks_option_allows_limiting_output()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task --number 1')
            ->assertStatus(200)
            ->assertDontSee('Low priority task')
            ->assertSee('High priority task')
            ->assertDontSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function task_command_searchterm_allows_searching_results()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task list High')
            ->assertStatus(200)
            ->assertDontSee('Low priority task')
            ->assertSee('High priority task')
            ->assertDontSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function if_there_are_no_tasks_say_nothing_to_do()
    {
        $this->user = User::factory()->create();

        $this->active_project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        $this->getUserResponse('task')
            ->assertStatus(200)
            ->assertSee('Nothing to do!');
    }

    /** @test */
    public function if_all_tasks_are_complete_then_say_so()
    {
        $this->user = User::factory()->create();

        $this->active_project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        Task::factory()->completed()->create([
            'description' => 'Completed task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
        ]);

        $this->getUserResponse('task')
            ->assertStatus(200)
            ->assertSee('All done!');
    }

    /** @test */
    public function can_filter_tasks_by_priority()
    {
        $this->makeTestTasks();
        $this->getUserResponse('task list --priority 10')
            ->assertOk()
            ->assertSee('High priority task')
            ->assertDontSee('Low priority task');
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

        $this->getUserResponse('task edit ' . Id::encode($task->id), $user)
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
}
