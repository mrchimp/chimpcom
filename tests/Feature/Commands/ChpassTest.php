<?php

namespace Tests\Feature\Commands;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ChpassTest extends TestCase
{
    /** @test */
    public function chpass_isnt_useable_by_guests()
    {
        $this->getGuestResponse('chpass')
            ->assertStatus(200)
            ->assertSee('You must log in to use this command.')
            ->assertSessionMissing('action');
    }

    /** @test */
    public function chpass_sets_chpass_1_action()
    {
        $this->getUserResponse('chpass')
            ->assertStatus(200)
            ->assertSee('Enter your new password. Type cancel to cancel.')
            ->assertSessionHas('action', 'chpass_1');
    }

    /** @test */
    public function actions_cant_be_accessed_as_commands()
    {
        $this->getUserResponse('chpass_1')
            ->assertStatus(404);
    }

    /** @test */
    public function chpass_1_action_aborts_if_cancel_is_provided()
    {
        $this->getUserResponse('chpass')
            ->assertSessionHas('action', 'chpass_1');

        $this->getUserResponse('cancel')
            ->assertSessionHas('action', 'normal');
    }

    /** @test */
    public function chpass_1_action_takes_a_password_and_sets_action_to_chpass_2()
    {
        $this->getUserResponse('chpass');

        $this
            ->withSession([
                'action' => 'chpass_1',
            ])
            ->getUserResponse('new password')
            ->assertSessionHas('action', 'chpass_2')
            ->assertSessionHas('chpass_1', 'new password');
    }

    /** @test */
    public function chpass_2_action_aborts_if_cancel_is_provided()
    {
        $this
            ->withSession([
                'action' => 'chpass_2',
                'chpass_1' => 'new password',
            ])
            ->getUserResponse('cancel')
            ->assertSee('Abandoning.')
            ->assertSessionHas('action', 'normal')
            ->assertSessionMissing('chpass_1');
    }

    /** @test */
    public function chpass_2_aborts_if_passwords_dont_match()
    {
        $this
            ->withSession([
                'action' => 'chpass_2',
                'chpass_1' => 'new password',
            ])
            ->getUserResponse('password that doesnt match')
            ->assertSee('Passwords did not match. Aborting.')
            ->assertSessionHas('action', 'normal')
            ->assertSessionMissing('chpass_1');
    }

    /** @test */
    public function completing_chpass_updates_user_password()
    {
        $this
            ->withSession([
                'action' => 'chpass_2',
                'chpass_1' => 'new password',
            ])
            ->getUserResponse('new password')
            ->assertSee('Ok then. All done.')
            ->assertSessionHas('action', 'normal')
            ->assertSessionMissing('chpass_1');

        $this->user->refresh();

        Auth::logout();

        $this
            ->withSession([
                'login_username' => $this->user->username,
                'action' => 'password',
            ])
            ->getGuestResponse('new password')
            ->assertSee('Welcome back.');
    }
}
