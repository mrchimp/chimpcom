<?php

namespace Tests\Feature\Actions;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PasswordActionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_provide_a_password_after_starting_login()
    {
        factory(User::class)->create([
            'name' => 'testuser',
            'password' => bcrypt('hunter2')
        ]);

        $this->getGuestResponse('login testuser')
            ->assertSee('Password')
            ->assertStatus(200)
            ->assertSessionHas('action', 'password');

        $this->getGuestResponse('hunter2')
            ->assertStatus(200)
            ->assertSee('Welcome back.')
            ->assertSessionHas('action', 'normal');
    }

    /** @test */
    public function user_cant_log_in_with_the_wrong_password()
    {
        factory(User::class)->create([
            'name' => 'testuser',
            'password' => bcrypt('hunter2')
        ]);

        $this->getGuestResponse('login testuser')
            ->assertSee('Password')
            ->assertStatus(200)
            ->assertSessionHas('action', 'password');

        $this->getGuestResponse('wrongpassword')
            ->assertStatus(200)
            ->assertSee('Hmmmm... No.')
            ->assertSessionHas('action', 'normal');
    }
}
