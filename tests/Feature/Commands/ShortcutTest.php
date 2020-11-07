<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Shortcut;
use Tests\TestCase;

class ShortcutTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function if_there_are_no_shortcuts_then_the_shortcuts_command_says_so()
    {
        $this->getGuestResponse('shortcuts')
            ->assertSee('There are currently no shortcuts.')
            ->assertStatus(200);
    }

    /** @test */
    public function shortcut_command_lists_shortcuts()
    {
        Shortcut::factory()->create([
            'name' => 'testshortcut',
        ]);

        $this->getGuestResponse('shortcuts')
            ->assertSee('testshortcut')
            ->assertStatus(200);
    }
}
