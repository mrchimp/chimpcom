<?php

namespace Tests\Feature\Commands;

use App\Mrchimp\Chimpcom\Id;
use App\User;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class ForgetTest extends TestCase
{
    /** @test */
    public function the_forget_command_is_not_for_noobs_or_guests_as_they_are_otherwise_known()
    {
        $this->getGuestResponse('forget thing')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function the_forget_command_has_some_joke_responses()
    {
        $this->getUserResponse('forget everything')
            ->assertStatus(200)
            ->assertSee('Where am I?');

        $this->getUserResponse('forget all')
            ->assertStatus(200)
            ->assertSee('Where am I?');
    }

    /** @test */
    public function you_cant_forget_a_message_that_doesnt_exist_or_did_you_forget_that_already()
    {
        $this->getUserResponse('forget memory_that_doesnt_exist')
            ->assertStatus(200)
            ->assertSee('Couldn\'t find that memory or it\'s not yours to forget.');
    }

    /** @test */
    public function if_memory_does_exist_then_the_action_is_queued_up()
    {
        $user = factory(User::class)->create();
        $memory = factory(Memory::class)->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('forget ' . $encoded_id, $user)
            ->assertStatus(200)
            ->assertSee('Are you sure')
            ->assertSessionHas('action', 'forget')
            ->assertSessionHas('forget_id');
    }

    /** @test */
    public function if_a_negative_is_given_the_forget_procedure_is_abbandonned_is_that_how_you_spell_abbandonned_whatever()
    {
        $user = factory(User::class)->create();
        $memory = factory(Memory::class)->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('forget ' . $encoded_id, $user);

        $this->getUserResponse('no', $user)
            ->assertStatus(200)
            ->assertSee('Action aborted.');
    }

    /** @test */
    public function if_an_unrecognised_response_is_given_the_forget_procedure_is_aborted_but_with_sass()
    {
        $user = factory(User::class)->create();
        $memory = factory(Memory::class)->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('forget ' . $encoded_id, $user);

        $this->getUserResponse('blblblblblblblblbl', $user)
            ->assertStatus(200)
            ->assertSee('Whatever.');
    }

    /** @test */
    public function if_an_affirmative_is_given_the_memory_is_forgotten()
    {
        $user = factory(User::class)->create();
        $memory = factory(Memory::class)->create([
            'user_id' => $user->id,
        ]);

        $encoded_id = Id::encode($memory->id);

        $this->getUserResponse('forget ' . $encoded_id, $user);

        $this->getUserResponse('yes', $user)
            ->assertStatus(200)
            ->assertSee('Memory forgotten');
    }
}
