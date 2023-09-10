<?php

namespace Tests\Feature\Commands;

use App\Mrchimp\Chimpcom\Id;
use App\User;
use Tests\TestCase;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Models\Project;

class TaskDoneTest extends TestCase
{
    protected $other_user;
    protected $active_project;
    protected $other_project;
    protected $other_users_project;
    protected $low_priority_task;
    protected $high_priority_task;
    protected $completed_task;
    protected $other_project_task;
    protected $other_users_task;

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

        $this->low_priority_task = Task::factory()->create([
            'description' => 'Low priority task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
            'priority' => 1,
        ]);

        $this->high_priority_task = Task::factory()->highpriority()->create([
            'description' => 'High priority task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
            'priority' => 10,
        ]);

        $this->completed_task = Task::factory()->completed()->create([
            'description' => 'Completed task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
        ]);

        $this->other_project_task = Task::factory()->create([
            'description' => 'Task on other project',
            'user_id' => $this->user->id,
            'project_id' => $this->other_project->id,
        ]);

        $this->other_users_task = Task::factory()->create([
            'description' => 'Other users task',
            'user_id' => $this->other_user->id,
            'project_id' => $this->other_users_project->id,
        ]);
    }

    /** @test */
    public function task_fails_for_guests()
    {
        $this->getGuestResponse('task:done foo')
            ->assertOk()
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function task_done_command_fails_if_user_has_no_active_project()
    {
        $this->getUserResponse('task:done 1')
            ->assertOk()
            ->assertSee('No active project');
    }

    /** @test */
    public function task_done_command_fails_if_task_cannot_be_found()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);
        $user->active_project_id = $project->id;
        $user->save();

        $this->getUserResponse('task:done 1', $user)
            ->assertOk()
            ->assertSee('Couldn\'t find that task.');
    }

    /** @test */
    public function task_done_command_cues_up_the_done_action_if_all_is_well()
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

        $this->getUserResponse('task:done 1', $user)
            ->assertOk()
            ->assertSee('Are you sure you want to mark as complete?');

        $this->assertAction('done');
    }

    /** @test */
    public function task_done_command_force_option_skips_confirmation()
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

        $this->getUserResponse('task:done 1 --force', $user)
            ->assertOk()
            ->assertSee('1 task completed.');
        $this->assertNoAction();

        $this->getUserResponse('task:done 2 -f', $user)
            ->assertOk()
            ->assertSee('1 task completed.');
        $this->assertNoAction();

        $this->assertEquals(0, Task::where('completed', 0)->count());
    }

    /** @test */
    public function task_done_command_can_mark_multiple_tasks_as_done_at_once_with_force()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task:done -f ' . Id::encode($this->high_priority_task->id) . ' ' . Id::encode($this->low_priority_task->id))
            ->assertOk();

        $task_high = Task::find($this->high_priority_task->id);
        $task_low = Task::find($this->low_priority_task->id);

        $this->assertEquals('1', $task_high->completed);
        $this->assertEquals('1', $task_low->completed);

        $this->getUserResponse('task')
            ->assertOk()
            ->assertDontSee($this->high_priority_task->description)
            ->assertDontSee($this->low_priority_task->description);
    }

    /** @test */
    public function task_done_command_can_mark_multiple_tasks_as_done_at_once_without_force()
    {
        $this->makeTestTasks();

        $this->getUserResponse('task:done -f ' . Id::encode($this->high_priority_task->id) . ' ' . Id::encode($this->low_priority_task->id))
            ->assertOk();

        $this->getUserResponse('ok', $this->user, $this->last_action_id);

        $task_high = Task::find($this->high_priority_task->id);
        $task_low = Task::find($this->low_priority_task->id);
        $this->assertEquals('1', $task_high->completed);
        $this->assertEquals('1', $task_low->completed);

        $this->getUserResponse('task')
            ->assertOk()
            ->assertDontSee($this->high_priority_task->description)
            ->assertDontSee($this->low_priority_task->description);
    }
}
