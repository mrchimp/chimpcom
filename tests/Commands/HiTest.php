<?php

namespace Tests\Commands;

class HiTest extends CommandTestTemplate
{
    public function testResponse()
    {
        $this->getGuestResponse('hi')
            ->assertStatus(200)
            ->assertSee('Chimpcom');
    }
}
