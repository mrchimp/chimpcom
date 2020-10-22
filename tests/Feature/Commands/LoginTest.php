<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function logged_in_users_cant_log_in_that_makes_no_sense()
    {
        $this->getUserResponse('login username')
            ->assertSee('You are already logged in as')
            ->assertStatus(200);
    }

    /** @test */
    public function if_a_username_doesnt_exist_then_you_cant_log_in_as_that_user()
    {
        $this->getGuestResponse('login mysteryman')
            ->assertSee('You fail.')
            ->assertStatus(200)
            ->assertSessionHas('action', 'normal');
    }

    /** @test */
    public function if_a_username_exists_you_can_start_the_login_process()
    {
        factory(User::class)->create([
            'name' => 'testuser',
        ]);

        $this->getGuestResponse('login testuser')
            ->assertSee('Password')
            ->assertStatus(200)
            ->assertSessionHas('action', 'password');
    }
}
