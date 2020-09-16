<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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
            ->assertSee('<div id="chimpcom"></div>', false);
    }
}
