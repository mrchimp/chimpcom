<?php

namespace Tests\Feature\Commands;

class AddShortcutTest extends CommandTestTemplate
{
    public function testGuestResponse()
    {
        $this->getGuestResponse('addshortcut foo http://example.com')
            ->assertStatus(200)
            ->assertSee('You must be logged in');
    }

    public function testUserResponse()
    {
        $this->getUserResponse('addshortcut foo http://example.com')
            ->assertStatus(200)
            ->assertSee('No.');
    }

    public function testAdminResponse()
    {
        $this->getAdminResponse('addshortcut foo http://example.com')
            ->assertStatus(200)
            ->assertSee('Ok.');
    }
}
