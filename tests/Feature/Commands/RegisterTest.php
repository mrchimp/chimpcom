<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function you_cant_register_if_youre_already_logged_in()
    {
        $this->getUserResponse('register blah')
            ->assertSee('You\'re already logged in.')
            ->assertOk();
    }

    /** @test */
    public function cant_register_with_username_that_already_exists()
    {
        User::factory()->create([
            'name' => 'fred',
        ]);

        $this->getGuestResponse('register fred')
            ->assertSee('There was a problem')
            ->assertOk();
    }

    /** @test */
    public function can_register_if_youre_smart()
    {
        $this->getGuestResponse('register fred')
            ->assertSee('Enter a password:')
            ->assertOk();
        $this->assertAction('register');
        $this->assertActionData([
            'register_username' => 'fred',
        ]);
    }
}
