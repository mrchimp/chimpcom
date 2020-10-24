<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UnameTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function uname_displays_system_details()
    {
        $this->getGuestResponse('uname')
            ->assertSee('Chimpcom')
            ->assertStatus(200);
    }

    /** @test */
    public function uname_can_show_all_at_once()
    {
        $this->getGuestResponse('uname --all')
            ->assertSee('Chimpcom')
            ->assertStatus(200);
    }
}
