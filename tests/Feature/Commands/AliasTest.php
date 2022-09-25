<?php

namespace Tests\Feature\Commands;

use Mrchimp\Chimpcom\Models\Alias;
use Tests\TestCase;

class AliasTest extends TestCase
{
    /** @test */
    public function GuestResponse()
    {
        $this->getGuestResponse('alias foo bar')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function UserResponse()
    {
        $this->getUserResponse('alias foo bar')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.not_admin'));
    }

    /** @test */
    public function AdminListResponse()
    {
        Alias::factory()->create([
            'name' => 'welcome',
            'alias' => 'hi',
        ]);

        $this->getAdminResponse('alias')
            ->assertStatus(200);
    }

    /** @test */
    public function AdminResponse()
    {
        $this->getAdminResponse('alias foo bar')
            ->assertStatus(200)
            ->assertSee('Ok.');
    }

    /** @test */
    public function admin_can_create_global_aliases()
    {
        $this->getAdminResponse('alias foo bar --global')
            ->assertOk();

        $alias = Alias::first();

        $this->assertNull($alias->user_id);
    }

    /** @test */
    public function user_cant_create_global_aliases()
    {
        $this->getUserResponse('alias foo bar --global')
            ->assertOk();

        $alias = Alias::first();

        $this->assertEquals($this->user->id, $alias->user_id);
    }
}
