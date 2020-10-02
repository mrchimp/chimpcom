<?php

namespace Tests\Feature\Commands;

class AliasTest extends CommandTestTemplate
{
    public function testGuestResponse()
    {
        $this->getGuestResponse('alias foo bar')
            ->assertStatus(200)
            ->assertSee('You must be logged in');
    }

    public function testUserResponse()
    {
        $this->getUserResponse('alias foo bar')
            ->assertStatus(200)
            ->assertSee('No.');
    }

    public function testAdminListResponse()
    {
        $this->getAdminResponse('alias')
            ->assertStatus(200);
    }

    public function testAdminResponse()
    {
        $this->getAdminResponse('alias foo bar')
            ->assertStatus(200)
            ->assertSee('Ok.');
    }
}
