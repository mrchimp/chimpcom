<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MonkeysTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function monkeys_command_says_some_shit()
    {
        $this->getGuestResponse('monkeys')
            ->assertStatus(200);
    }
}
