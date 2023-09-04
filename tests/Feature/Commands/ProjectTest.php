<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_user_project_command()
    {
        $this->getGuestResponse('project')
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function if_there_is_no_active_project_there_is_a_message()
    {
        $this->user = User::factory()->create();

        $this->getUserResponse('project')
            ->assertSee('No active project')
            ->assertOk();
    }

    /** @test */
    public function can_show_current_project()
    {
        $this->user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);
        $this->user->active_project_id = $project->id;
        $this->user->save();

        $this->getUserResponse('project')
            ->assertSee('Current project: myproject')
            ->assertOk();
    }
}
