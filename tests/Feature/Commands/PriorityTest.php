<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Task;
use Tests\TestCase;

class PriorityTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function priority_is_not_for_guests()
    {
        $this->getGuestResponse('priority a a')
            ->assertSee('You must be logged in to use this command.')
            ->assertStatus(200);
    }

    /** @test */
    public function priority_must_be_an_integer()
    {
        $this->getUserResponse('priority 1 not_a_number')
            ->assertSee('Priority should be an integer.')
            ->assertStatus(200);
    }

    /** @test */
    public function users_can_set_priority_on_tasks()
    {
        $task = factory(Task::class)->create();
        $user = factory(User::class)->create();

        $this->getUserResponse('priority ' . $task->id . ' 10', $user)
            ->assertStatus(200)
            ->assertSee('Ok.');
    }

    /** @test */
    public function cant_set_priority_on_a_task_that_doesnt_exist()
    {
        $this->getUserResponse('priority 99999 10')
            ->assertSee('Couldn\'t find that task, or it\'s not yours to edit.')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_set_priority_on_a_task_you_dont_own()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'user_id' => 9999,
        ]);

        $this->getUserResponse('priority ' . $task->id . ' 10')
            ->assertStatus(200)
            ->assertSee('Couldn\'t find that task, or it\'s not yours to edit.');
    }
}
