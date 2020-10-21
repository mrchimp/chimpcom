<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class HexdecTest extends TestCase
{
    /** @test */
    public function hexdec_command_turns_hex_into_decimal()
    {
        $this->getGuestResponse('hexdec f')
            ->assertStatus(200)
            ->assertSee('15');
    }

    /** @test */
    public function hexdec_can_take_values_with_a_hash()
    {
        $this->getGuestResponse('hexdec #fff')
            ->assertStatus(200)
            ->assertSee('rgb(255, 255, 255)');

        $this->getGuestResponse('hexdec #ffffff')
            ->assertStatus(200)
            ->assertSee('rgb(255, 255, 255)');
    }

    /** @test */
    public function hexdec_will_fail_if_theres_a_hash_but_a_weird_number_of_characters()
    {
        $this->getGuestResponse('hexdec #fffffff')
            ->assertStatus(200)
            ->assertSee('I don\'t know how to handle this.');
    }
}
