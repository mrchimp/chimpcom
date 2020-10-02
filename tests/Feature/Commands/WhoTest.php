<?php

namespace Tests\Feature\Commands;

class WhoTest extends CommandTestTemplate
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
