<?php

namespace Tests\Feature\Commands;

use App\User;
use Carbon\Carbon;
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

    /** @test */
    public function can_get_a_list_of_words()
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

        $this->getUserResponse('show --words', $user)
            ->assertSee('one')
            ->assertSee('two')
            ->assertSee('three')
            ->assertStatus(200);
    }

    /** @test */
    public function can_list_only_public_memories()
    {
        $user = factory(User::class)->create();

        factory(Memory::class)->states('public')->create([
            'name' => 'test',
            'content' => 'Public memory',
            'user_id' => $user->id,
        ]);

        factory(Memory::class)->create([
            'name' => 'test',
            'content' => 'Private memory',
            'user_id' => $user->id
        ]);

        $this->getUserResponse('show test --public', $user)
            ->assertSee('Public memory')
            ->assertDontSee('Private memory')
            ->assertStatus(200);
    }

    /** @test */
    public function can_list_only_private_memories()
    {
        $user = factory(User::class)->create();

        factory(Memory::class)->states('public')->create([
            'name' => 'test',
            'content' => 'Public memory',
            'user_id' => $user->id,
        ]);

        factory(Memory::class)->create([
            'name' => 'test',
            'content' => 'Private memory',
            'user_id' => $user->id
        ]);

        $this->getUserResponse('show test --private', $user)
            ->assertSee('Private memory')
            ->assertDontSee('Public memory')
            ->assertStatus(200);
    }

    /** @test */
    public function can_show_only_my_memories()
    {
        $user = factory(User::class)->create();

        factory(Memory::class)->states('public')->create([
            'name' => 'test',
            'content' => 'My memory',
            'user_id' => $user->id,
        ]);

        factory(Memory::class)->states('public')->create([
            'name' => 'test',
            'content' => 'Others memory',
            'user_id' => 9999
        ]);

        $this->getUserResponse('show test --mine', $user)
            ->assertSee('My memory')
            ->assertDontSee('Others memory')
            ->assertStatus(200);
    }

    /** @test */
    public function memories_can_be_ordered_by_date()
    {
        $old = factory(Memory::class)->create([
            'created_at' => Carbon::now()->subYear(),
            'name' => 'test',
            'content' => 'Older memory',
        ]);

        $new = factory(Memory::class)->create([
            'created_at' => Carbon::now(),
            'name' => 'test',
            'content' => 'Recent memory',
        ]);

        $old->updated_at = Carbon::now()->subYear();
        $old->created_at = Carbon::now()->subYear();
        $old->save(['timestamps' => false]);

        $new->updated_at = Carbon::now();
        $new->created_at = Carbon::now();
        $new->save(['timestamps' => false]);

        $this->getUserResponse('show test --last')
            ->assertSeeInOrder([
                'Recent memory',
                'Older memory',
            ])
            ->assertDontSee('Others memory')
            ->assertStatus(200);
    }
}
