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
    public function users_can_create_new_projects()
    {
        $this->getUserResponse('project new testproject')
            ->assertSee('Creating project')
            ->assertOk();
        $this->assertAction('newproject');

        $this->getUserResponse('Description of my project', null, $this->last_action_id)
            ->assertSee('Project saved and set as current project.')
            ->assertOk();
        $this->assertNoAction();

        $project = Project::first();

        $this->user->refresh();

        $this->assertEquals('testproject', $project->name);
        $this->assertEquals('Description of my project', $project->description);
        $this->assertEquals($this->user->active_project_id, $project->id);
    }

    /** @test */
    public function user_can_set_their_active_project()
    {
        $this->user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(0, $this->user->active_project_id);

        $this->getUserResponse('project set myproject')
            ->assertSee('myproject is now the current project')
            ->assertOk();

        $this->assertEquals($this->user->active_project_id, $project->id);
    }

    /** @test */
    public function users_can_remove_their_projects()
    {
        $this->user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);

        $this->getUserResponse('project rm myproject')
            ->assertSee('Are you sure you want to delete the project')
            ->assertOk();

        $this->assertAction('project_rm');
        $this->assertActionData([
            'projectrm' => $project->id,
        ]);

        $this->getUserResponse('no', null, $this->last_action_id)
            ->assertSee('Fair enough.');

        $this->getUserResponse('project rm myproject', null, $this->last_action_id);

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

        $this->getUserResponse('project rm myproject')
            ->assertSee('Cannot remove that.');
        $this->assertNoAction();
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
    public function projects_names_that_arent_alphadash_will_be_rejected()
    {
        $this->getUserResponse('project new £&^&^&*')
            ->assertSee('There was a problem')
            ->assertOk();
    }

    /** @test */
    public function cant_set_current_project_if_it_cant_be_found()
    {
        $this->getUserResponse('project set lkdsajflasfdhjkfds')
            ->assertSee('That project ID is invalid')
            ->assertOk();
    }

    /** @test */
    public function can_list_current_project()
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

    /** @test */
    public function can_get_a_commands_tab_completions()
    {
        $this->user = User::factory()->create();

        Project::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'chimpcom',
        ]);

        $json = $this->actingAs($this->user)
            ->get('/ajax/tabcomplete?cmd_in=project+set+c')
            ->assertOk()
            ->json();

        $this->assertCount(1, $json);
        $this->assertEquals('project set chimpcom', $json[0]);
    }

    /** @test */
    public function will_get_empty_array_if_tab_completion_fails()
    {
        $this->user = User::factory()->create();

        Project::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'chimpcom',
        ]);

        $json = $this->actingAs($this->user)
            ->get('/ajax/tabcomplete?cmd_in=project+set+xxxx')
            ->assertOk()
            ->json();

        $this->assertCount(0, $json);
    }

    /** @test */
    public function projects_lists_your_projects()
    {
        $user = User::factory()->create();
        Project::factory()->create([
            'name' => 'Project Name'
        ]);

        $this->getUserResponse('project list', $user)
            ->assertSee('Project Name')
            ->assertOk();
    }

    /** @test */
    public function projects_cant_list_projects_if_you_dont_have_any_projects()
    {
        $this->getUserResponse('project list')
            ->assertSee('No projects.')
            ->assertOk();
    }
}
