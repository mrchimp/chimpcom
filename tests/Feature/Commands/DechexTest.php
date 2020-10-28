<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class DechexTest extends TestCase
{
    /** @test */
    public function dechex_converts_decimal_to_hex()
    {
        $this->getGuestResponse('dechex 128')
            ->assertStatus(200)
            ->assertSee('80');
    }

    /** @test */
    public function dexhex_can_take_css_rgb_values()
    {
        $response = $this->getGuestResponse('dechex rgb(0,128,255)')
            ->assertStatus(200)
            ->assertSee('#080ff');

        $this->assertStringContainsString(
            '<span style=\"color:#080ff\">',
            $response->getContent()
        );
    }
}
