<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Shortcut;
use Tests\TestCase;

class ShortcutsTest extends TestCase
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
        $this->user = User::factory()->create();

        Shortcut::factory()->create([
            'name' => 'global',
            'user_id' => null,
        ]);

        Shortcut::factory()->create([
            'name' => 'private',
            'user_id' => $this->user->id,
        ]);

        $this->getGuestResponse('shortcuts')
            ->assertSee('global')
            ->assertDontSee('private')
            ->assertStatus(200);

        $this->getUserResponse('shortcuts')
            ->assertSee('global')
            ->assertSee('private');
    }
}
