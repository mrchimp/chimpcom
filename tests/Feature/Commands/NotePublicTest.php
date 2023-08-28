<?php

namespace Tests\Feature\Commands;

use App\Mrchimp\Chimpcom\Id;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Memory as Note;
use Tests\TestCase;

class NotePublicTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function setpublic_is_not_for_guests()
    {
        $this->getGuestResponse('note:public asd')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertStatus(200);
    }

    /** @test */
    public function setpublic_doesnt_work_on_memories_that_dont_exist()
    {
        $this->getUserResponse('note:public asd')
            ->assertSee('That note doesn\'t exist.')
            ->assertStatus(200);
    }

    /** @test */
    public function setpublic_doesnt_work_on_other_peoples_memories()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => 9999,
        ]);

        $this->getUserResponse('note:public 1', $user)
            ->assertDontSee('Ok.')
            ->assertStatus(200);

        $note->refresh();

        $this->assertEquals(0, $note->public);
    }

    /** @test */
    public function setpublic_marks_memories_as_public()
    {
        $user = User::factory()->create();
        Note::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $this->getUserResponse('note:public 1 2', $user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $this->assertEquals(2, Note::where('public', 1)->count());
    }
}
