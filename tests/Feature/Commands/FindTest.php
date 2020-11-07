<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class FindTest extends TestCase
{
    /** @test */
    public function find_command_finds_memory_by_name()
    {
        $user = User::factory()->create();

        Memory::factory()->public()->create([
            'name' => 'memory_name',
            'content' => 'Memory Content',
            'user_id' => $user->id,
        ]);

        $this
            ->getGuestResponse('find memory_name')
            ->assertStatus(200)
            ->assertSee('Memory Content');
    }

    /** @test */
    public function private_memories_cant_be_seen_by_other_users()
    {
        $user = User::factory()->create();

        Memory::factory()->create([
            'name' => 'memory_name',
            'content' => 'Memory Content',
            'user_id' => $user->id,
        ]);

        $this
            ->getGuestResponse('find memory_name')
            ->assertStatus(200)
            ->assertDontSee('Memory Content');
    }

    /** @test */
    public function private_memories_can_be_seen_by_owner()
    {
        $user = User::factory()->create();

        Memory::factory()->create([
            'name' => 'memory_name',
            'content' => 'Memory Content',
            'user_id' => $user->id,
        ]);

        $this
            ->getUserResponse('find memory_name', $user)
            ->assertStatus(200)
            ->assertSee('Memory Content');
    }

    /** @test */
    public function find_commands_public_flag_shows_only_public_memories()
    {
        $user = User::factory()->create();

        Memory::factory()->create([
            'name' => 'memory_name',
            'content' => 'Private Memory',
            'user_id' => $user->id,
        ]);

        Memory::factory()->public()->create([
            'name' => 'memory_name',
            'content' => 'Public Memory',
            'user_id' => $user->id,
        ]);

        $this
            ->getUserResponse('find memory_name --public', $user)
            ->assertStatus(200)
            ->assertSee('Public Memory')
            ->assertDontSee('Private Memory');
    }

    /** @test */
    public function find_commands_private_flag_shows_only_private_memories()
    {
        $user = User::factory()->create();

        Memory::factory()->create([
            'name' => 'memory_name',
            'content' => 'Private Memory',
            'user_id' => $user->id,
        ]);

        Memory::factory()->public()->create([
            'name' => 'memory_name',
            'content' => 'Public Memory',
            'user_id' => $user->id,
        ]);

        $this
            ->getUserResponse('find memory_name --private', $user)
            ->assertStatus(200)
            ->assertSee('Private Memory')
            ->assertDontSee('Public Memory');
    }

    /** @test */
    public function find_commands_mine_flag_only_shows_memories_owned_by_the_current_user()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        Memory::factory()->create([
            'name' => 'memory_name',
            'content' => 'My Memory',
            'user_id' => $user->id,
        ]);

        Memory::factory()->create([
            'name' => 'memory_name',
            'content' => 'Other Persons Memory',
            'user_id' => $other_user->id,
        ]);

        $this
            ->getUserResponse('find memory_name --mine',  $user)
            ->assertStatus(200)
            ->assertSee('My Memory')
            ->assertDontSee('Other Persons Memory');
    }
}
