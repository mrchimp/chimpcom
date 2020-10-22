<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Message;
use Tests\TestCase;

class MailTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function you_must_log_in_to_use_mail_command()
    {
        $this->getGuestResponse('mail')
            ->assertSee('You must be logged in to use this command')
            ->assertStatus(200);
    }

    /** @test */
    public function ids_must_be_given_if_trying_to_delete_mail_messages()
    {
        $this->getUserResponse('mail --delete')
            ->assertSee('No message IDs given.')
            ->assertStatus(200);
    }

    /** @test */
    public function a_user_can_delete_messages_they_received()
    {
        $user = factory(User::class)->create();
        $message = factory(Message::class)->create([
            'recipient_id' => $user->id,
        ]);

        $this->getUserResponse('mail --delete ' . $message->id, $user)
            ->assertSee('Message(s) deleted.')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_delete_messages_with_invalid_ids()
    {
        $user = factory(User::class)->create();

        $this->getUserResponse('mail --delete 9999999', $user)
            ->assertSee('There was a problem.')
            ->assertStatus(200);
    }

    /** @test */
    public function users_cant_read_messages_if_nobody_has_sent_them_any()
    {
        $this->getUserResponse('mail')
            ->assertSee('No messages')
            ->assertStatus(200);
    }

    /** @test */
    public function users_can_read_messages_sent_to_them()
    {
        $user = factory(User::class)->create();
        factory(Message::class)->create([
            'recipient_id' => $user->id,
            'message' => 'Here is a message.',
        ]);

        $this->getUserResponse('mail', $user)
            ->assertSee('Here is a message')
            ->assertStatus(200);
    }
}
