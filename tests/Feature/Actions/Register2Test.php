<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class Register2Test extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->getGuestResponse('register fred');
        $this->getGuestResponse('hunter22');
    }

    /** @test */
    public function providing_the_same_password_again_continues_and_asks_for_an_email_address()
    {
        $this->getGuestResponse('hunter22')
            ->assertSee('Your email address:')
            ->assertStatus(200)
            ->assertSessionHas('action', 'register3');
    }

    /** @test */
    public function if_user_or_password_in_session_is_missing_then_abort()
    {
        Session::forget('register_username');

        $this->getGuestResponse('hunter22')
            ->assertSee('This should not happen.')
            ->assertStatus(200)
            ->assertSessionMissing('register_username')
            ->assertSessionMissing('register_password')
            ->assertSessionHas('action', 'normal');
    }
}
