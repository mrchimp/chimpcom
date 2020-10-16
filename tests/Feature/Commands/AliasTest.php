<?php

namespace Tests\Feature\Commands;

use Mrchimp\Chimpcom\Models\Alias;
use Tests\TestCase;

class AliasTest extends TestCase
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
        factory(Alias::class)->create([
            'name' => 'welcome',
            'alias' => 'hi',
        ]);

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
