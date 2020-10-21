<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class DoechoTest extends TestCase
{
    /** @test */
    public function doecho_returns_the_given_parameter()
    {
        $this->getGuestResponse('doecho blah')
            ->assertStatus(200)
            ->assertSee('blah');
    }
}
