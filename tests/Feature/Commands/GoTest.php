<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GoTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function go_command_redirects_to_given_url()
    {
        $json = $this->getGuestResponse('go https://example.com')
            ->assertStatus(200)
            ->json();

        $this->assertEquals('https://example.com', $json['redirect']);
    }

    /** @test */
    public function go_command_appends_https_if_it_needs_to()
    {
        $json = $this->getGuestResponse('go example.com')
            ->assertStatus(200)
            ->json();

        $this->assertEquals('https://example.com', $json['redirect']);
    }
}
