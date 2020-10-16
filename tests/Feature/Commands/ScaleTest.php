<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ScaleTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_a_musical_scale()
    {
        $this->getGuestResponse('scale c major')
            ->assertSee('C, D, E, F, G, A, B')
            ->assertStatus(200);

        $this->getGuestResponse('scale c harmonic minor')
            ->assertSee('C, D, D#, F, G, G#, B')
            ->assertStatus(200);
    }

    /** @test */
    public function can_list_all_scales()
    {
        $this->getGuestResponse('scale c')
            ->assertSee('major')
            ->assertSee('minor')
            ->assertSee('harmonic minor')
            ->assertStatus(200);
    }

    /** @test */
    public function invalid_root_notes_are_invalid()
    {
        $this->getGuestResponse('scale x minor')
            ->assertSee('That is not a valid root note');
    }
}
