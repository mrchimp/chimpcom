<?php

namespace Tests\Commands;

class HiTest extends CommandTestTemplate
{
    public function testResponse()
    {
        $this->getResponse('hi')
            ->assertStatus(200)
            ->assertSee('Chimpcom');
    }
}
