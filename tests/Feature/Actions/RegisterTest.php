<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function register_action_accepts_a_password()
    {
        $this->getGuestResponse('register fred');

        $this->getGuestResponse('hunter22', $this->last_action_id)
            ->assertSee('Enter the same password again:')
            ->assertOk();
        $this->assertAction('register2');
        $this->assertActionData([
            'register_password' => 'hunter22'
        ]);
    }

    /** @test */
    public function register_action_aborts_if_a_password_is_not_provided()
    {
        $this->getGuestResponse('register fred');
        $this->getGuestResponse('', $this->last_action_id)
            ->assertSee('No password given. Giving up.')
            ->assertOk();
        $this->assertNoAction();
    }
}
