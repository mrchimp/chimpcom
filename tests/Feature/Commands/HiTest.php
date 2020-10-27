<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class HiTest extends TestCase
{
    /** @test */
    public function guests_can_say_hi()
    {
        $this->getGuestResponse('hi')
            ->assertStatus(200)
            ->assertSee('Chimpcom');
    }

    /** @test */
    public function users_can_say_hi_too()
    {
        $this->getUserResponse('hi')
            ->assertSee('Welcome back')
            ->assertStatus(200);
    }
}
