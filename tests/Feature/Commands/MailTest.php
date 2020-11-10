<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Message;
use Tests\TestCase;

class MailTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function you_must_log_in_to_use_mail_command()
    {
        $this->getGuestResponse('mail')
            ->assertSee(__('chimpcom.must_log_in'))
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
        $user = User::factory()->create();
        $message = Message::factory()->create([
            'recipient_id' => $user->id,
        ]);

        $this->getUserResponse('mail --delete ' . $message->id, $user)
            ->assertSee('Message(s) deleted.')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_delete_messages_with_invalid_ids()
    {
        $user = User::factory()->create();

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
        $user = User::factory()->create();
        Message::factory()->create([
            'recipient_id' => $user->id,
            'message' => 'Here is a message.',
        ]);

        $this->getUserResponse('mail', $user)
            ->assertSee('Here is a message')
            ->assertStatus(200);
    }

    /** @test */
    public function reading_messages_doesnt_mark_them_as_read()
    {
        $user = User::factory()->create();

        Message::factory()->create([
            'recipient_id' => $user->id,
            'message' => 'Here is a message.',
        ]);

        $this->assertEquals(1, $user->messages()->whereUnread()->count());

        $this->getUserResponse('mail', $user);

        $this->assertEquals(1, $user->messages()->whereUnread()->count());
    }

    /** @test */
    public function using_the_r_flag_marks_messages_as_read()
    {
        $user = User::factory()->create();

        Message::factory()->create([
            'recipient_id' => $user->id,
            'message' => 'Here is a message.',
        ]);

        $this->assertEquals(1, $user->messages()->whereUnread()->count());

        $this->getUserResponse('mail -r', $user);

        $this->assertEquals(0, $user->messages()->whereUnread()->count());
    }

    /** @test */
    public function using_the_outbox_flag_can_show_sent_messages()
    {
        $author = User::factory()->create(['name' => 'Author']);
        $recipient = User::factory()->create(['name' => 'Recipient']);

        Message::factory()->create([
            'recipient_id' => $recipient->id,
            'author_id' => $author->id,
            'message' => 'This was sent by Author to Recipient.',
        ]);

        $this->getUserResponse('mail --outbox', $author)
            ->assertStatus(200)
            ->assertSee('This was sent by Author to Recipient.');
    }
}
