<?php

namespace Tests\Feature\Commands;

use App\Mrchimp\Chimpcom\Id;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Memory;
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
            ->assertSee('That memory doesn\'t exist.')
            ->assertStatus(200);
    }

    /** @test */
    public function setpublic_doesnt_work_on_other_peoples_memories()
    {
        $user = User::factory()->create();
        $memory = Memory::factory()->create([
            'user_id' => 9999,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('note:public ' . $encoded_id, $user)
            ->assertSee('That isn\'t your memory to change.')
            ->assertStatus(200);

        $memory->refresh();

        $this->assertEquals(0, $memory->public);
    }

    /** @test */
    public function setpublic_marks_memories_as_public()
    {
        $user = User::factory()->create();
        $memory = Memory::factory()->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('note:public ' . $encoded_id, $user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $memory->refresh();

        $this->assertEquals(1, $memory->public);
    }
}
