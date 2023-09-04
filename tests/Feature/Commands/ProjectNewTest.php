<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class ProjectNewTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function users_can_create_new_projects()
    {
        $this->getUserResponse('project:new testproject')
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
    public function projects_names_that_arent_alphadash_will_be_rejected()
    {
        $this->getUserResponse('project:new £&^&^&*')
            ->assertSee('There was a problem')
            ->assertOk();
    }
}
