<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ManTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function man_gets_a_commands_help_text()
    {
        $this->getGuestResponse('man man')
            ->assertSee('Gets help on a given command')
            ->assertStatus(200);
    }
}
