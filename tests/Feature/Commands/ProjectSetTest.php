<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class ProjectSetTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_set_their_active_project()
    {
        $this->user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(0, $this->user->active_project_id);

        $this->getUserResponse('project:set myproject')
            ->assertSee('myproject is now the current project')
            ->assertOk();

        $this->assertEquals($this->user->active_project_id, $project->id);
    }

    /** @test */
    public function cant_set_current_project_if_it_cant_be_found()
    {
        $this->getUserResponse('project:set lkdsajflasfdhjkfds')
            ->assertSee('That project ID is invalid')
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
            ->get('/ajax/tabcomplete?cmd_in=project:set+c')
            ->assertOk()
            ->json();

        $this->assertCount(1, $json);
        $this->assertEquals('project:set chimpcom', $json[0]);
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
            ->get('/ajax/tabcomplete?cmd_in=project:set+xxxx')
            ->assertOk()
            ->json();

        $this->assertCount(0, $json);
    }
}
