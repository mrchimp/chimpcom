<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Message;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function message_can_send_a_message_to_a_person()
    {
        $author = factory(User::class)->create(['name' => 'author']);
        $recipient = factory(User::class)->create(['name' => 'recipient']);

        $this->getUserResponse('message @recipient Hello there!', $author)
            ->assertSee('Message sent!')
            ->assertStatus(200);

        $messages = Message::all();

        $this->assertCount(1, $messages);
        $this->assertEquals('Hello there!', $messages->first()->message);
        $this->assertEquals($author->id, $messages->first()->author_id);
        $this->assertEquals($recipient->id, $messages->first()->recipient_id);
    }

    /** @test */
    public function can_use_at_symbol_message_shorthand()
    {
        $author = factory(User::class)->create(['name' => 'author']);
        $recipient = factory(User::class)->create(['name' => 'recipient']);

        $this->getUserResponse('@recipient Hello there!', $author)
            ->assertSee('Message sent!')
            ->assertStatus(200);

        $messages = Message::all();

        $this->assertCount(1, $messages);
        $this->assertEquals('Hello there!', $messages->first()->message);
        $this->assertEquals($author->id, $messages->first()->author_id);
        $this->assertEquals($recipient->id, $messages->first()->recipient_id);
    }

    /** @test */
    public function message_command_cant_send_a_message_if_no_recipient_is_provided()
    {
        $this->getUserResponse('message no names here')
            ->assertSee('You need to tell me who to send that to')
            ->assertStatus(200);
    }

    /** @test */
    public function message_cant_send_a_message_to_a_non_person()
    {
        $author = factory(User::class)->create(['name' => 'author']);
        $recipient = factory(User::class)->create(['name' => 'recipient']);

        $this->getUserResponse('message @recipient_who_doesnt_exist Hello there!', $author)
            ->assertSee('Error sending message.')
            ->assertStatus(200);

        $messages = Message::all();

        $this->assertEmpty($messages);
    }

    /** @test */
    public function message_can_send_messages_to_multiple_people()
    {
        $author = factory(User::class)->create(['name' => 'author']);
        factory(User::class)->create(['name' => 'recipient_1']);
        factory(User::class)->create(['name' => 'recipient_2']);

        $this->getUserResponse('message @recipient_1 @recipient_2 Hello there!', $author)
            ->assertSee('All messages were sent!')
            ->assertStatus(200);

        $messages = Message::all();

        $this->assertCount(2, $messages);
    }

    /** @test */
    public function if_message_only_sends_to_some_people_then_the_message_says_so()
    {
        $author = factory(User::class)->create(['name' => 'author']);
        factory(User::class)->create(['name' => 'recipient_1']);

        $this->getUserResponse('message @recipient_1 @recipient_2_doesnt_exist Hello there!', $author)
            ->assertSee('Sent messages with ~50% success rate.')
            ->assertStatus(200);

        $messages = Message::all();

        $this->assertCount(1, $messages);
    }

    /** @test */
    public function if_attempting_to_send_multiple_messages_and_they_all_fail_then_get_a_message_about_that()
    {
        $author = factory(User::class)->create(['name' => 'author']);
        $this->getUserResponse('message @recipient_1_doesnt_exist @recipient_2_doesnt_exist_either Hello there!', $author)
            ->assertSee('No messages were sent.')
            ->assertStatus(200);

        $messages = Message::all();

        $this->assertEmpty($messages);
    }
}
