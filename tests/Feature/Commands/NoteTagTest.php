<?php

namespace Tests\Feature\Commands;

use App\User;
use Tests\TestCase;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Project;

class NoteTagTest extends TestCase
{
    protected $other_user;

    protected $active_project;

    protected function makeTestNotes()
    {
        $this->user = User::factory()->create();

        $this->active_project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->active_project_id = $this->active_project->id;
        $this->user->save();

        Memory::factory()->create([
            'name' => 'First',
            'content' => 'Here is a public memory.',
            'user_id' => $this->user->id,
            'public' => 1,
        ]);

        Memory::factory()->create([
            'name' => 'Second',
            'content' => 'This is a private memory.',
            'user_id' => $this->user->id,
            'public' => 0,
        ]);
    }

    /** @test */
    public function note_fails_for_guests()
    {
        $this->getGuestResponse('note:tag')
            ->assertStatus(404)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function can_add_or_remove_tag_to_a_note()
    {
        $this->makeTestNotes();

        $this->getUserResponse("note:tag 1 2 @foo @bar")
            ->assertOk();

        $this->getUserResponse("note First")
            ->assertOk()
            ->assertSee("First")
            ->assertDontSee('Second')
            ->assertSee("@foo")
            ->assertSee("@bar");
        $this->getUserResponse("note Second")
            ->assertOk()
            ->assertSee('Second')
            ->assertDontSee('First')
            ->assertSee("@foo")
            ->assertSee("@bar");

        $this->getUserResponse("note:tag --remove 1 2 @foo @bar")
            ->assertOk();

        $this->getUserResponse("note First")
            ->assertOk()
            ->assertSee("First")
            ->assertDontSee("Second")
            ->assertDontSee("@foo")
            ->assertDontSee("@bar");

        $this->getUserResponse("note Second")
            ->assertOk()
            ->assertSee("Second")
            ->assertDontSee("First")
            ->assertDontSee("@foo")
            ->assertDontSee("@bar");
    }
}
