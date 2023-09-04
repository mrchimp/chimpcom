<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class ProjectRmTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function users_can_remove_their_projects()
    {
        $this->user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);

        $this->getUserResponse('project:rm myproject')
            ->assertSee('Are you sure you want to delete the project')
            ->assertOk();

        $this->assertAction('project_rm');
        $this->assertActionData([
            'projectrm' => $project->id,
        ]);

        $this->getUserResponse('no', null, $this->last_action_id)
            ->assertSee('Fair enough.');

        $this->getUserResponse('project:rm myproject', null, $this->last_action_id);

        $this->getUserResponse('yes', null, $this->last_action_id)
            ->assertSee('Ok. It\'s gone.')
            ->assertOk();
    }

    /** @test */
    public function users_cannot_remove_other_peoples_projects()
    {
        $this->user = User::factory()->create();
        $other_user = User::factory()->create();

        Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $other_user->id,
        ]);

        $this->getUserResponse('project:rm myproject')
            ->assertSee('Cannot remove that.');
        $this->assertNoAction();
    }
}
