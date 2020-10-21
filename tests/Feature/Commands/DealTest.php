<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class DealTest extends TestCase
{
    /** @test */
    public function deal_can_be_called()
    {
        $this->getGuestResponse('deal')
            ->assertStatus(200);
    }

    /** @test */
    public function deal_can_take_count_option()
    {
        $this->getGuestResponse('deal --count 10')
            ->assertStatus(200);
    }
}
