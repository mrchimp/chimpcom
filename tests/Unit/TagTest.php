<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Tag;
use Tests\TestCase;

class TagTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_tags_from_string()
    {
        $input = 'This #string has #some #tags in #it this#isnt#a#tag';

        $tags = Tag::fromString($input);

        $this->assertIsArray($tags);
        $this->assertContains('string', $tags);
        $this->assertContains('some', $tags);
        $this->assertContains('tags', $tags);
        $this->assertContains('it', $tags);
    }
}
