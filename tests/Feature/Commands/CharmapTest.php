<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class CharmapTest extends TestCase
{
    /** @test */
    public function charmap_returns_some_characters_for_some_reason()
    {
        $this->getGuestResponse('charmap')
            ->assertStatus(200);
    }

    /** @test */
    public function charmap_can_take_a_count_option()
    {
        $this->getGuestResponse('charmap --count=10')
            ->assertStatus(200);
    }

    /** @test */
    public function charmap_can_take_a_show_numbers_option()
    {
        $this->getGuestResponse('charmap --show_numbers')
            ->assertStatus(200);
    }
}
