<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VersionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function version_command_gets_the_version()
    {
        $this->getGuestResponse('version v7.0b')
            ->assertSee('Chimpcom')
            ->assertStatus(200);
    }
}
