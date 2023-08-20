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
        $this->getGuestResponse('hunter22', $this->last_action_id);
    }

    /** @test */
    public function providing_the_same_password_again_continues_and_asks_for_an_email_address()
    {
        $this->getGuestResponse('hunter22', $this->last_action_id)
            ->assertSee('Your email address:')
            ->assertOk();
        $this->assertAction('register3');
    }
}
