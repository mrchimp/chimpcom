<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPageLoads()
    {
        $this
            ->get('/')
            ->assertStatus(200)
            ->assertSee('<div id="cmd"></div>', false);
    }
}
