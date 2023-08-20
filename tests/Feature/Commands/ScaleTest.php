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
            ->assertOk()
            ->assertSee('C, D, E, F, G, A, B');

        $this->getGuestResponse('scale c harmonic minor')
            ->assertOk()
            ->assertSee('C, D, D#, F, G, G#, B');
    }

    /** @test */
    public function can_list_all_scales()
    {
        $this->getGuestResponse('scale c')
            ->assertOk()
            ->assertSee('major')
            ->assertSee('minor')
            ->assertSee('harmonic minor');
    }

    /** @test */
    public function invalid_root_notes_are_invalid()
    {
        $this->getGuestResponse('scale x minor')
            ->assertOk()
            ->assertSee('That is not a valid root note');
    }
}
