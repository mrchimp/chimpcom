<?php

namespace Tests\Feature\Commands;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Models\Memory as Note;

class NoteTest extends TestCase
{
    /** @test */
    public function show_returns_memories_that_match()
    {
        $user = User::factory()->create();

        Note::factory()->create([
            'name' => 'match',
            'content' => 'This is a match',
            'user_id' => $user->id,
        ]);
        Note::factory()->create([
            'name' => 'non_match',
            'content' => 'This is not a match',
            'user_id' => $user->id,
        ]);

        $this->getUserResponse('note match', $user)
            ->assertStatus(200)
            ->assertSee('This is a match')
            ->assertDontSee('This is not a match');
    }

    /** @test */
    public function show_can_accept_multiple_search_words()
    {
        $user = User::factory()->create();

        Note::factory()->create([
            'name' => 'one',
            'content' => 'Note one',
            'user_id' => $user->id
        ]);

        Note::factory()->create([
            'name' => 'two',
            'content' => 'Note two',
            'user_id' => $user->id
        ]);

        Note::factory()->create([
            'name' => 'three',
            'content' => 'Note three',
            'user_id' => $user->id
        ]);

        $this->getUserResponse('note one two', $user)
            ->assertStatus(200)
            ->assertSee('Note one')
            ->assertSee('Note two')
            ->assertDontSee('Note three');
    }

    /** @test */
    public function can_get_a_list_of_words()
    {
        $user = User::factory()->create();

        Note::factory()->create([
            'name' => 'one',
            'content' => 'Note one',
            'user_id' => $user->id
        ]);

        Note::factory()->create([
            'name' => 'two',
            'content' => 'Note two',
            'user_id' => $user->id
        ]);

        Note::factory()->create([
            'name' => 'three',
            'content' => 'Note three',
            'user_id' => $user->id
        ]);

        $this->getUserResponse('note --words', $user)
            ->assertSee('one')
            ->assertSee('two')
            ->assertSee('three')
            ->assertStatus(200);
    }

    /** @test */
    public function can_list_only_public_memories()
    {
        $user = User::factory()->create();

        Note::factory()->public()->create([
            'name' => 'test',
            'content' => 'Public note',
            'user_id' => $user->id,
        ]);

        Note::factory()->create([
            'name' => 'test',
            'content' => 'Private note',
            'user_id' => $user->id
        ]);

        $this->getUserResponse('note test --public', $user)
            ->assertSee('Public note')
            ->assertDontSee('Private note')
            ->assertStatus(200);
    }

    /** @test */
    public function can_list_only_private_memories()
    {
        $user = User::factory()->create();

        Note::factory()->public()->create([
            'name' => 'test',
            'content' => 'Public note',
            'user_id' => $user->id,
        ]);

        Note::factory()->create([
            'name' => 'test',
            'content' => 'Private note',
            'user_id' => $user->id
        ]);

        $this->getUserResponse('note test --private', $user)
            ->assertSee('Private note')
            ->assertDontSee('Public note')
            ->assertStatus(200);
    }

    /** @test */
    public function can_show_only_my_memories()
    {
        $user = User::factory()->create();

        Note::factory()->public()->create([
            'name' => 'test',
            'content' => 'My note',
            'user_id' => $user->id,
        ]);

        Note::factory()->public()->create([
            'name' => 'test',
            'content' => 'Others note',
            'user_id' => 9999
        ]);

        $this->getUserResponse('note test --mine', $user)
            ->assertSee('My note')
            ->assertDontSee('Others note')
            ->assertStatus(200);
    }

    /** @test */
    public function memories_can_be_ordered_by_date()
    {
        $old = Note::factory()->create([
            'created_at' => Carbon::now()->subYear(),
            'name' => 'test',
            'content' => 'Older note',
        ]);

        $new = Note::factory()->create([
            'created_at' => Carbon::now(),
            'name' => 'test',
            'content' => 'Recent note',
        ]);

        $old->updated_at = Carbon::now()->subYear();
        $old->created_at = Carbon::now()->subYear();
        $old->save(['timestamps' => false]);

        $new->updated_at = Carbon::now();
        $new->created_at = Carbon::now();
        $new->save(['timestamps' => false]);

        $this->getUserResponse('note test --date')
            ->assertSeeInOrder([
                'Recent note',
                'Older note',
            ])
            ->assertDontSee('Others note')
            ->assertStatus(200);
    }

    /** @test */
    public function can_fetch_a_random_note()
    {
        $this->markAsRisky('This relies on randomness so could flake but the odds are pretty good...');

        $user = User::factory()->create();

        Note::factory()->create([
            'name' => 'test',
            'content' => 'blerpblerpblerp',
            'public' => 1,
            'user_id' => $user->id,
        ]);

        Note::factory()->create([
            'name' => 'test',
            'content' => 'flumflumflum',
            'public' => 1,
            'user_id' => $user->id,
        ]);

        $one_count = 0;
        $two_count = 0;

        for ($i = 0; $i < 50; $i++) {
            $json = $this->getGuestResponse('note -r -n 1 test')->json();

            if (Str::contains($json['cmd_out'], 'blerpblerpblerp')) {
                $one_count++;
            } elseif (Str::contains($json['cmd_out'], 'flumflumflum')) {
                $two_count++;
            }
        }

        $this->assertGreaterThan(1, $one_count);
        $this->assertGreaterThan(1, $two_count);
    }
}
