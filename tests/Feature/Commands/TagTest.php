<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Tag;
use Tests\TestCase;

class TagTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function tag_command_lists_commands()
    {
        Tag::factory()->create([
            'tag' => 'green',
        ]);
        Tag::factory()->create([
            'tag' => 'blue',
        ]);

        $this->getUserResponse('tag')
            ->assertSee('green')
            ->assertSee('blue')
            ->assertStatus(200);
    }

    /** @test */
    public function tag_command_can_list_a_tags_memories()
    {
        $tag = Tag::factory()->create([
            'tag' => 'blue',
        ]);

        $memory = Memory::factory()->create([
            'name' => 'Memory',
            'content' => 'Here is a sentence',
        ]);

        $tag->memories()->save($memory);

        $this->getUserResponse('tag blue')
            ->assertSee('Here is a sentence')
            ->assertStatus(200);
    }
}
