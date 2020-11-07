<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\Task;
use Tests\TestCase;

class DoneTest extends TestCase
{
    /** @test */
    public function done_command_is_not_for_guests()
    {
        $this->getGuestResponse('done 1')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function done_command_fails_if_user_has_no_active_project()
    {
        $this->getUserResponse('done 1')
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

        $this->getUserResponse('done 1', $user)
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
        $task = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->getUserResponse('done 1', $user)
            ->assertStatus(200)
            ->assertSee('Are you sure you want to mark this as complete?')
            ->assertSessionHas('action', 'done');
    }


}
