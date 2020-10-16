<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class Base64encodeTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_encode_base64()
    {
        $this->getGuestResponse('base64encode hello')
            ->assertSee('aGVsbG8=')
            ->assertStatus(200);
    }
}
