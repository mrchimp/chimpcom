<?php

namespace Tests\Feature\Actions;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\Message;
use Tests\TestCase;

class PasswordActionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_provide_a_password_after_starting_login()
    {
        User::factory()->create([
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

        $this->assertTrue(Auth::check());
    }

    /** @test */
    public function user_cant_log_in_with_the_wrong_password()
    {
        User::factory()->create([
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

    /** @test */
    public function if_a_user_has_unread_mail_they_will_be_told_on_login()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => bcrypt('hunter2')
        ]);

        Message::factory()->create([
            'recipient_id' => $user->id,
        ]);

        $this->getGuestResponse('login testuser')
            ->assertSee('Password')
            ->assertStatus(200)
            ->assertSessionHas('action', 'password');

        $this->getGuestResponse('hunter2')
            ->assertStatus(200)
            ->assertSee('You have 1 unread message.')
            ->assertSessionHas('action', 'normal');
    }

    /** @test */
    public function if_a_user_has_multiple_unread_messages_the_grammar_will_still_work()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => bcrypt('hunter2')
        ]);

        Message::factory()->count(2)->create([
            'recipient_id' => $user->id,
        ]);

        $this->getGuestResponse('login testuser')
            ->assertSee('Password')
            ->assertStatus(200)
            ->assertSessionHas('action', 'password');

        $this->getGuestResponse('hunter2')
            ->assertStatus(200)
            ->assertSee('You have 2 unread messages.')
            ->assertSessionHas('action', 'normal');
    }
}
