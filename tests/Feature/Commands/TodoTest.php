<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\Task;
use Tests\TestCase;

class TodoTest extends TestCase
{
    protected $other_user;

    protected $active_project;

    protected function makeTestTasks()
    {
        $this->user = factory(User::class)->create();
        $this->other_user = factory(User::class)->create();

        $this->active_project = factory(Project::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->other_project = factory(Project::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->other_users_project = factory(Project::class)->create([
            'user_id' => $this->other_user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        $this->other_user->active_project_id = $this->other_users_project->id;
        $this->other_user->save();

        factory(Task::class)->create([
            'description' => 'Low priority task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
        ]);

        factory(Task::class)->states('highpriority')->create([
            'description' => 'High priority task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
        ]);

        factory(Task::class)->states('completed')->create([
            'description' => 'Completed task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
        ]);

        factory(Task::class)->create([
            'description' => 'Task on other project',
            'user_id' => $this->user->id,
            'project_id' => $this->other_project->id,
        ]);

        factory(Task::class)->create([
            'description' => 'Other users task',
            'user_id' => $this->other_user->id,
            'project_id' => $this->other_users_project->id,
        ]);
    }

    /** @test */
    public function todo_fails_for_guests()
    {
        $this->getGuestResponse('todo')
            ->assertStatus(404)
            ->assertSee('You must be logged in');
    }

    /** @test */
    public function todo_returns_error_if_no_active_project()
    {
        $this->user = factory(User::class)->create();
        $this->user->active_project_id = 0;
        $this->user->save();

        $this->getUserResponse('todo')
            ->assertStatus(200)
            ->assertSee('No active project.');
    }

    /** @test */
    public function by_default_todo_shows_incomplete_tasks_on_current_project()
    {
        $this->makeTestTasks();

        $this->getUserResponse('todo')
            ->assertStatus(200)
            ->assertSee('Low priority task')
            ->assertSee('High priority task')
            ->assertDontSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function todo_command_with_all_option_shows_completed_and_uncompleted_tasks()
    {
        $this->makeTestTasks();

        $this->getUserResponse('todo --all')
            ->assertStatus(200)
            ->assertSee('Low priority task')
            ->assertSee('High priority task')
            ->assertSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function todo_command_with_allprojects_option_shows_tasks_from_all_users_projects()
    {
        $this->makeTestTasks();

        $this->getUserResponse('todo --allprojects')
            ->assertStatus(200)
            ->assertSee('Low priority task')
            ->assertSee('High priority task')
            ->assertDontSee('Completed task')
            ->assertSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function todo_command_with_completed_option_shows_tasks_from_all_users_projects()
    {
        $this->makeTestTasks();

        $this->getUserResponse('todo --complete')
            ->assertStatus(200)
            ->assertDontSee('Low priority task')
            ->assertDontSee('High priority task')
            ->assertSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function todo_command_num_tasks_option_allows_limiting_output()
    {
        $this->makeTestTasks();

        $this->getUserResponse('todo --number 1')
            ->assertStatus(200)
            ->assertDontSee('Low priority task')
            ->assertSee('High priority task')
            ->assertDontSee('Completed task')
            ->assertDontSee('Task on other project')
            ->assertDontSee('Other users task');
    }

    /** @test */
    public function todo_command_searchterm_allows_searching_results()
    {
        $this->makeTestTasks();

        $this->getUserResponse('todo High')
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
        $this->user = factory(User::class)->create();

        $this->active_project = factory(Project::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        $this->getUserResponse('todo')
            ->assertStatus(200)
            ->assertSee('Nothing to do!');
    }

    /** @test */
    public function if_all_tasks_are_complete_then_say_so()
    {
        $this->user = factory(User::class)->create();

        $this->active_project = factory(Project::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        factory(Task::class)->states('completed')->create([
            'description' => 'Completed task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
        ]);

        $this->getUserResponse('todo')
            ->assertStatus(200)
            ->assertSee('All done!');
    }
}
