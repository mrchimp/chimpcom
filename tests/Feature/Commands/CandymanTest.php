<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CandymanTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_say_candyman_three_times_for_a_surprise()
    {
        $this->getUserResponse('candyman')
            ->assertStatus(200)
            ->assertSessionHas('action', 'candyman');

        $this->getUserResponse('candyman')
            ->assertStatus(200)
            ->assertSee('AAAAAAAAGH!');
    }

    /** @test */
    public function saying_candyman_only_once_is_disappointing()
    {
        $this->getUserResponse('candyman')
            ->assertStatus(200)
            ->assertSessionHas('action', 'candyman');

        $this->getUserResponse('not today')
            ->assertStatus(200)
            ->assertSee('Pussy');
    }
}
