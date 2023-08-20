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
            ->assertOk()
            ->assertSee(__('chimpcom.must_log_in'));

        $this->assertNoAction();
    }

    /** @test */
    public function chpass_sets_chpass_1_action()
    {
        $this->getUserResponse('chpass')
            ->assertOk()
            ->assertSee('Enter your new password. Type cancel to cancel.');

        $this->assertAction('chpass_1');
    }

    /** @test */
    public function actions_cant_be_accessed_as_commands()
    {
        $this->getUserResponse('chpass_1')
            ->assertNotFound();
    }

    /** @test */
    public function chpass_1_action_aborts_if_cancel_is_provided()
    {
        $this->getUserResponse('chpass');
        $this->assertAction('chpass_1');

        $this->getUserResponse('cancel', null, $this->last_action_id);
        $this->assertNoAction();
    }

    /** @test */
    public function chpass_1_action_takes_a_password_and_sets_action_to_chpass_2()
    {
        $this->getUserResponse('chpass');
        $this->getUserResponse('new password', null, $this->last_action_id);
        $this->assertAction('chpass_2');
        $this->assertActionData([
            'chpass_1' => 'new password',
        ]);
    }

    /** @test */
    public function chpass_2_action_aborts_if_cancel_is_provided()
    {
        $action_id = $this->setAction('chpass_2', [
            'chpass_1' => 'new password',
        ]);

        $this
            ->getUserResponse('cancel', null, $action_id)
            ->assertSee('Abandoning.');
        $this->assertNoAction();
        $this->assertActionDoesntExist($action_id);
    }

    /** @test */
    public function chpass_2_aborts_if_passwords_dont_match()
    {
        $action_id = $this->setAction(
            'chpass_2',
            [
                'chpass_1' => 'new password',
            ]
        );

        $this
            ->getUserResponse('password that doesnt match', null, $action_id)
            ->assertSee('Passwords did not match. Aborting.');

        $this->assertNoAction();
        $this->assertActionDoesntExist($action_id);
    }

    /** @test */
    public function completing_chpass_updates_user_password()
    {
        $action_id = $this->setAction(
            'chpass_2',
            [
                'chpass_1' => 'new password',
            ]
        );

        $this
            ->getUserResponse('new password', null, $action_id)
            ->assertSee('Ok then. All done.');

        $this->assertNoAction();
        $this->assertActionDoesntExist($action_id);

        $this->user->refresh();

        Auth::logout();

        $action_id = $this->setAction(
            'password',
            [
                'username' => $this->user->name,
            ]
        );

        $this
            ->getGuestResponse('new password', $action_id)
            ->assertSee('Welcome back.');
    }
}
