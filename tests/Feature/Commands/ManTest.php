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

    /** @test */
    public function man_can_list_commands()
    {
        $this->getGuestResponse('man --commands')
            ->assertSee('login')
            ->assertSee('logout')
            ->assertSee('man')
            ->assertStatus(200);
    }

    /** @test */
    public function man_with_no_arguments_describes_itself()
    {
        $this->getGuestResponse('man')
            ->assertSee('This is how you get help')
            ->assertStatus(200);
    }

    /** @test */
    public function man_doesnt_provide_help_on_commands_that_dont_exist()
    {
        $this->getGuestResponse('man zxcvzxcvzxcvzxcvzxcvxzcv')
            ->assertSee('No man page found')
            ->assertStatus(200);
    }
}
