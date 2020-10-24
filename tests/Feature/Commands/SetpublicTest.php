<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class SetpublicTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function setpublic_is_not_for_guests()
    {
        $this->getGuestResponse('setpublic asd')
            ->assertSee('You must log in to use this command.')
            ->assertStatus(200);
    }

    /** @test */
    public function setpublic_doesnt_work_on_memories_that_dont_exist()
    {
        $this->getUserResponse('setpublic asd')
            ->assertSee('That memory doesn\'t exist.')
            ->assertStatus(200);
    }

    /** @test */
    public function setpublic_doesnt_work_on_other_peoples_memories()
    {
        $user = factory(User::class)->create();
        $memory = factory(Memory::class)->create([
            'user_id' => 9999,
        ]);

        $encoded_id = Chimpcom::encodeId($memory->id);

        $this->getUserResponse('setpublic ' . $encoded_id, $user)
            ->assertSee('That isn\'t your memory to change.')
            ->assertStatus(200);

        $memory->refresh();

        $this->assertEquals(0, $memory->public);
    }

    /** @test */
    public function setpublic_marks_memories_as_public()
    {
        $user = factory(User::class)->create();
        $memory = factory(Memory::class)->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Chimpcom::encodeId($memory->id);

        $this->getUserResponse('setpublic ' . $encoded_id, $user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $memory->refresh();

        $this->assertEquals(1, $memory->public);
    }
}
