<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class DoesTest extends TestCase
{
    /** @test */
    public function does_command_is_just_so_funny()
    {
        $this->getGuestResponse('does blah')
            ->assertStatus(200)
            ->assertSee('Questions end with question marks.');
    }

    /** @test */
    public function does_command_is_super_helpful()
    {
        $json = $this->getGuestResponse('does this work?')
            ->assertStatus(200)
            ->json();

        $this->assertContains($json['cmd_out'], [
            'I\'m not sure yet. ',
            'Sometimes. ',
            'Usually. ',
            'Sort of. ',
            'It depends how you look at it. '
        ]);
    }
}
