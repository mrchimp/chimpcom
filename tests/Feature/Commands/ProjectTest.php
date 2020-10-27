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
            ->assertSessionHas('action', 'newproject')
            ->assertStatus(200);

        $this->getUserResponse('Description of my project')
            ->assertSee('Project saved and set as current project.')
            ->assertStatus(200)
            ->assertSessionHas('action', 'normal');

        $project = Project::first();

        $this->user->refresh();

        $this->assertEquals('testproject', $project->name);
        $this->assertEquals('Description of my project', $project->description);
        $this->assertEquals($this->user->active_project_id, $project->id);
    }

    /** @test */
    public function user_can_set_their_active_project()
    {
        $this->user = factory(User::class)->create();
        $project = factory(Project::class)->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(0, $this->user->active_project_id);

        $this->getUserResponse('project set myproject')
            ->assertSee('myproject is now the current project')
            ->assertStatus(200);

        $this->assertEquals($this->user->active_project_id, $project->id);
    }

    /** @test */
    public function users_can_remove_their_projects()
    {
        $this->user = factory(User::class)->create();
        $project = factory(Project::class)->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);

        $this->getUserResponse('project rm myproject')
            ->assertSee('Are you sure you want to delete the project')
            ->assertStatus(200)
            ->assertSessionHas('action', 'project_rm')
            ->assertSessionHas('projectrm', $project->id);

        $this->getUserResponse('no')
            ->assertSee('Fair enough.');

        $this->getUserResponse('project rm myproject');

        $this->getUserResponse('yes')
            ->assertSee('Ok. It\'s gone.')
            ->assertStatus(200);
    }

    /** @test */
    public function users_cannot_remove_other_peoples_projects()
    {
        $this->user = factory(User::class)->create();
        $other_user = factory(User::class)->create();

        factory(Project::class)->create([
            'name' => 'myproject',
            'user_id' => $other_user->id,
        ]);

        $this->getUserResponse('project rm myproject')
            ->assertSee('Cannot remove that.')
            ->assertSessionMissing('action');
    }

    /** @test */
    public function if_there_is_no_active_project_there_is_a_message()
    {
        $this->user = factory(User::class)->create();

        $this->getUserResponse('project')
            ->assertSee('No active project')
            ->assertStatus(200);
    }

    /** @test */
    public function projects_names_that_arent_alphadash_will_be_rejected()
    {
        $this->getUserResponse('project new £&^&^&*')
            ->assertSee('There was a problem')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_set_current_project_if_it_cant_be_found()
    {
        $this->getUserResponse('project set lkdsajflasfdhjkfds')
            ->assertSee('That project ID is invalid')
            ->assertStatus(200);
    }

    /** @test */
    public function can_list_current_project()
    {
        $this->user = factory(User::class)->create();
        $project = factory(Project::class)->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);
        $this->user->active_project_id = $project->id;
        $this->user->save();

        $this->getUserResponse('project')
            ->assertSee('Current project: myproject')
            ->assertStatus(200);
    }

    /** @test */
    public function can_get_a_commands_tab_completions()
    {
        $this->user = factory(User::class)->create();

        factory(Project::class)->create([
            'user_id' => $this->user->id,
            'name' => 'chimpcom',
        ]);

        $json = $this->actingAs($this->user)
            ->get('/ajax/tabcomplete?cmd_in=project+set+c')
            ->assertStatus(200)
            ->json();

        $this->assertCount(1, $json);
        $this->assertEquals('project set chimpcom', $json[0]);
    }

    /** @test */
    public function will_get_empty_array_if_tab_completion_fails()
    {
        $this->user = factory(User::class)->create();

        factory(Project::class)->create([
            'user_id' => $this->user->id,
            'name' => 'chimpcom',
        ]);

        $json = $this->actingAs($this->user)
            ->get('/ajax/tabcomplete?cmd_in=project+set+xxxx')
            ->assertStatus(200)
            ->json();

        $this->assertCount(0, $json);
    }
}
