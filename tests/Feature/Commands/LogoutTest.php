<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function you_cant_log_out_if_you_never_log_in()
    {
        $this->getGuestResponse('logout')
            ->assertSee('You\'re not logged in.')
            ->assertStatus(200);
    }

    /** @test */
    public function users_can_log_out()
    {
        $this->getUserResponse('logout')
            ->assertStatus(200)
            ->assertSee('You are now logged out.');

        $this->assertFalse(Auth::check());
    }
}
