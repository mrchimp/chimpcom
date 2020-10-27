<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CommandListTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_a_command_list()
    {
        $this->get('ajax/commands')
            ->assertStatus(200);
    }
}
