<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class SaveTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_use_save_command()
    {
        $this->getGuestResponse('save name content')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertStatus(200);
    }

    /** @test */
    public function users_can_save_memories()
    {
        $this->getUserResponse('save name Here is the content')
            ->assertSee('Memory saved.')
            ->assertStatus(200);

        $memory = Memory::first();

        $this->assertEquals('name', $memory->name);
        $this->assertEquals('Here is the content', $memory->content);
    }
}
