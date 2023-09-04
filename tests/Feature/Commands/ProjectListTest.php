<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class ProjectListTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function project_lists_your_projects()
    {
        $user = User::factory()->create();
        Project::factory()->create([
            'name' => 'Project Name'
        ]);

        $this->getUserResponse('project:list', $user)
            ->assertSee('Project Name')
            ->assertOk();
    }

    /** @test */
    public function project_cant_list_projects_if_you_dont_have_any_projects()
    {
        $this->getUserResponse('project:list')
            ->assertSee('No projects.')
            ->assertOk();
    }
}
