<?php

namespace Tests\Feature\Commands;

use App\Mrchimp\Chimpcom\Id;
use App\User;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class NoteForgetTest extends TestCase
{
    /** @test */
    public function the_forget_command_is_not_for_noobs_or_guests_as_they_are_otherwise_known()
    {
        $this->getGuestResponse('note:forget thing')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function the_forget_command_has_some_joke_responses()
    {
        $this->getUserResponse('note:forget everything')
            ->assertStatus(200)
            ->assertSee('Where am I?');

        $this->getUserResponse('note:forget all')
            ->assertStatus(200)
            ->assertSee('Where am I?');
    }

    /** @test */
    public function you_cant_forget_a_message_that_doesnt_exist_or_did_you_forget_that_already()
    {
        $this->getUserResponse('note:forget memory_that_doesnt_exist')
            ->assertStatus(200)
            ->assertSee('Couldn\'t find that memory or it\'s not yours to forget.');
    }

    /** @test */
    public function if_memory_does_exist_then_the_action_is_queued_up()
    {
        $user = User::factory()->create();
        $memory = Memory::factory()->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('note:forget ' . $encoded_id, $user)
            ->assertStatus(200)
            ->assertSee('Are you sure');

        $this->assertAction('forget');
        $this->assertActionData(['forget_id' => $encoded_id]);
    }

    /** @test */
    public function if_a_negative_is_given_the_forget_procedure_is_abbandonned_is_that_how_you_spell_abbandonned_whatever()
    {
        $user = User::factory()->create();
        $memory = Memory::factory()->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('note:forget ' . $encoded_id, $user);

        $this->getUserResponse('no', $user, $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('Action aborted.');
        $this->assertNoAction();
    }

    /** @test */
    public function if_an_unrecognised_response_is_given_the_forget_procedure_is_aborted_but_with_sass()
    {
        $user = User::factory()->create();
        $memory = Memory::factory()->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('note:forget ' . $encoded_id, $user);

        $this->getUserResponse('blblblblblblblblbl', $user, $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('Whatever.');
        $this->assertNoAction();
    }

    /** @test */
    public function if_an_affirmative_is_given_the_memory_is_forgotten()
    {
        $user = User::factory()->create();
        $memory = Memory::factory()->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('note:forget ' . $encoded_id, $user);

        $this->getUserResponse('yes', $user, $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('Memory forgotten');
        $this->assertNoAction();
    }
}
