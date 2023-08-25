<?php

namespace Tests\Feature\Commands;

use App\User;
use Tests\TestCase;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Models\Project;

class TaskTagsTest extends TestCase
{
    protected $other_user;

    protected $active_project;

    protected function makeTestTasks()
    {
        $this->user = User::factory()->create();

        $this->active_project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        Task::factory()->create([
            'description' => 'Test task',
            'user_id' => $this->user->id,
            'project_id' => $this->active_project->id,
            'priority' => 1,
        ]);
    }

    /** @test */
    public function can_add_or_remove_tag_to_a_task()
    {
        $this->makeTestTasks();
        $this->getUserResponse("task addtag 1 @foo")
            ->assertOk();
        $this->getUserResponse("task")
            ->assertOk()
            ->assertSee("@foo");

        $this->getUserResponse("task removetag 1 @foo")
            ->assertOk();
        $this->getUserResponse("task")
            ->assertOk()
            ->assertDontSee("@foo");
    }
}
