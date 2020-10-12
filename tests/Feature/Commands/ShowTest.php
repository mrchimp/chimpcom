<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class ShowTest extends TestCase
{
    /** @test */
    public function show_returns_memories_that_match()
    {
        $user = factory(User::class)->create();

        factory(Memory::class)->create([
            'name' => 'match',
            'content' => 'This is a match',
            'user_id' => $user->id,
        ]);
        factory(Memory::class)->create([
            'name' => 'non_match',
            'content' => 'This is not a match',
            'user_id' => $user->id,
        ]);

        $this->getUserResponse('show match', $user)
            ->assertStatus(200)
            ->assertSee('This is a match')
            ->assertDontSee('This is not a match');
    }

    /** @test */
    public function show_can_accept_multiple_search_words()
    {
        $user = factory(User::class)->create();

        factory(Memory::class)->create([
            'name' => 'one',
            'content' => 'Memory one',
            'user_id' => $user->id
        ]);

        factory(Memory::class)->create([
            'name' => 'two',
            'content' => 'Memory two',
            'user_id' => $user->id
        ]);

        factory(Memory::class)->create([
            'name' => 'three',
            'content' => 'Memory three',
            'user_id' => $user->id
        ]);

        $this->getUserResponse('show one two', $user)
            ->assertStatus(200)
            ->assertSee('Memory one')
            ->assertSee('Memory two')
            ->assertDontSee('Memory three');
    }
}
