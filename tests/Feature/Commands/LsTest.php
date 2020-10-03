<?php

namespace Tests\Feature\Commands;

use Mrchimp\Chimpcom\Models\Directory;
use Tests\TestCase;

class LsTest extends TestCase
{
    /** @test */
    public function ls_returns_error_if_there_are_no_directories()
    {
        $this->getGuestResponse('ls')
            ->assertStatus(200)
            ->assertSee('File system unavailable');
    }

    /** @test */
    public function ls_shows_current_directory_listing_for_guests()
    {
        $parent = factory(Directory::class)->create();
        $child = factory(Directory::class)->create();

        $parent->appendNode($child);

        $this->getGuestResponse('ls')
            ->assertStatus(200)
            ->assertSee($child->name);
    }
}
