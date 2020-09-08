<?php

namespace Tests\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Memory;

class FindTest extends CommandTestTemplate
{
    use DatabaseMigrations;

    /** @test */
    public function find_command_finds_memory_by_name()
    {
        $user = factory(User::class)->create();

        factory(Memory::class)->states('public')->create([
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
        $user = factory(User::class)->create();

        factory(Memory::class)->create([
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
        $user = factory(User::class)->create();

        factory(Memory::class)->create([
            'name' => 'memory_name',
            'content' => 'Memory Content',
            'user_id' => $user->id,
        ]);

        $this
            ->getUserResponse('find memory_name', $user)
            ->assertStatus(200)
            ->assertSee('Memory Content');
    }
}
