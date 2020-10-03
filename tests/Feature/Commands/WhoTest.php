<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class WhoTest extends TestCase
{
    public function testGuestResponse()
    {
        $this->getGuestResponse('who')
            ->assertStatus(200)
            ->assertSee('USERAGENT');
    }

    public function testUserResponse()
    {
        $this->getUserResponse('who')
            ->assertStatus(200)
            ->assertSee('USERNAME')
            ->assertSee($this->user->name);
    }

    public function testAgentResponse()
    {
        $this->getAdminResponse('who')
            ->assertStatus(200)
            ->assertSee('USERNAME')
            ->assertSee($this->admin->name);
    }
}
