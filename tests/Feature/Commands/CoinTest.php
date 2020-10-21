<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class CoinTest extends TestCase
{
    /** @test */
    public function coin_flips_a_coin()
    {
        $possible_answers = ['Heads', 'Tails'];

        $json = $this->getGuestResponse('coin')
            ->assertStatus(200)
            ->json();

        $this->assertContains($json['cmd_out'], $possible_answers);

        // It's a random answer so lets test a bunch of times.
        $this->assertContains($this->getGuestResponse('coin')->json()['cmd_out'], $possible_answers);
        $this->assertContains($this->getGuestResponse('coin')->json()['cmd_out'], $possible_answers);
        $this->assertContains($this->getGuestResponse('coin')->json()['cmd_out'], $possible_answers);
        $this->assertContains($this->getGuestResponse('coin')->json()['cmd_out'], $possible_answers);
        $this->assertContains($this->getGuestResponse('coin')->json()['cmd_out'], $possible_answers);
    }
}
