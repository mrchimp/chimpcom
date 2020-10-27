<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\Task;
use Tests\TestCase;

class NewtaskTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function newtask_command_is_not_for_guests()
    {
        $this->getGuestResponse('newtask Do a thing')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertStatus(200);
    }

    /** @test */
    public function newtask_fails_if_user_has_no_active_project()
    {
        $this->getUserResponse('newtask Do a thing')
            ->assertSee('No active project')
            ->assertStatus(200);
    }

    /** @test */
    public function newtask_can_create_a_new_task()
    {
        $user = factory(User::class)->create();
        $project = factory(Project::class)->create();
        $user->setActiveProject($project);

        $this->getUserResponse('newtask Do a thing', $user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $task = Task::first();

        $this->assertEquals(1, Task::count());

        $this->assertEquals('Do a thing', $task->description);
        $this->assertEquals($user->id, $task->user_id);
        $this->assertEquals(0, $task->completed);
        $this->assertEquals(1, $task->priority);
    }

    /** @test */
    public function newtask_can_set_a_high_priority_on_tasks()
    {
        $user = factory(User::class)->create();
        $project = factory(Project::class)->create();
        $user->setActiveProject($project);

        $this->getUserResponse('newtask Do an important thing --priority 10', $user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $this->assertEquals(10, Task::first()->priority);
    }
}
