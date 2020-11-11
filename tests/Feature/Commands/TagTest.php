<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Tag;
use Tests\TestCase;

class TagTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function tag_command_lists_commands()
    {
        $green = Tag::factory()->create([
            'tag' => 'green',
        ]);
        $blue = Tag::factory()->create([
            'tag' => 'blue',
        ]);

        $this->getUserResponse('tag')
            ->assertSee('green')
            ->assertSee('blue')
            ->assertStatus(200);
    }
}
