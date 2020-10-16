<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class Base64decodeTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_decode_base64()
    {
        $this->getGuestResponse('base64decode aGVsbG8=')
            ->assertSee('hello')
            ->assertStatus(200);
    }
}
