<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class HiTest extends TestCase
{
    public function testResponse()
    {
        $this->getGuestResponse('hi')
            ->assertStatus(200)
            ->assertSee('Chimpcom');
    }
}
