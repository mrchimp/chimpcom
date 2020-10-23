<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function register_action_accepts_a_password()
    {
        $this->getGuestResponse('register fred');

        $this->getGuestResponse('hunter22')
            ->assertSee('Enter the same password again:')
            ->assertStatus(200)
            ->assertSessionHas('action', 'register2')
            ->assertSessionHas('register_password', 'hunter22');
    }

    /** @test */
    public function register_action_fails_if_register_username_is_not_set()
    {
        $this->getGuestResponse('register fred');

        Session::forget('register_username');

        $this->getGuestResponse('hunter22')
            ->assertSee('This should not happen')
            ->assertSessionMissing('register_username')
            ->assertStatus(200);
    }

    /** @test */
    public function register_action_aborts_if_a_password_is_not_provided()
    {
        $this->getGuestResponse('register fred');

        $this->getGuestResponse('')
            ->assertSee('No password given. Giving up.')
            ->assertSessionMissing('register_username')
            ->assertStatus(200);
    }
}
