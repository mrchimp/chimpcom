<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function projects_lists_your_projects()
    {
        $user = User::factory()->create();
        Project::factory()->create([
            'name' => 'Project Name'
        ]);

        $this->getUserResponse('projects', $user)
            ->assertSee('Project Name')
            ->assertStatus(200);
    }

    /** @test */
    public function projects_cant_list_projects_if_you_dont_have_any_projects()
    {
        $this->getUserResponse('projects')
            ->assertSee('No projects.')
            ->assertStatus(200);
    }

    /** @test */
    public function projects_is_not_for_guests()
    {
        $this->getGuestResponse('projects')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertStatus(200);
    }
}
