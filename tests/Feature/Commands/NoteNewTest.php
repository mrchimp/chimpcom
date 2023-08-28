<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class NoteNewTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_use_save_command()
    {
        $this->getGuestResponse('note:new name content')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertStatus(200);
    }

    /** @test */
    public function users_can_save_memories()
    {
        $this->getUserResponse('note:new name Here is the content')
            ->assertSee('Memory saved.')
            ->assertStatus(200);

        $memory = Memory::first();

        $this->assertEquals('name', $memory->name);
        $this->assertEquals('Here is the content', $memory->content);
    }

    /** @test */
    public function memories_can_be_tagged()
    {
        $this->getUserResponse('note:new name Here is @some content @hashtag')
            ->assertStatus(200);

        $memory = Memory::first();

        $this->assertCount(2, $memory->tags);
    }
}
