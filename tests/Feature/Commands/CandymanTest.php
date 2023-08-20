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
            ->assertStatus(200);
        $this->assertAction('candyman');

        $this->getUserResponse('candyman', null, $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('AAAAAAAAGH!');
        $this->assertNoAction();
    }

    /** @test */
    public function saying_candyman_only_once_is_disappointing()
    {
        $this->getUserResponse('candyman')
            ->assertStatus(200);
        $this->assertAction('candyman');

        $this->getUserResponse('not today', null, $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('Pussy');
        $this->assertNoAction();
    }
}
