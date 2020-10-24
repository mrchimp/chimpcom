<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TetrisTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function tetris_gets_tetrinos()
    {
        $json = $this->getGuestResponse('tetris')
            ->assertStatus(200)
            ->json();

        $tetrino = substr($json['cmd_out'], 20);
        $tetrino = substr($tetrino, 0, -6);

        $this->assertContains($tetrino, [
            '&#x25A0;&#x25A0;&#x25A0;&#x25A0;',                     // Line
            '&#x25A0;<br>&#x25A0;<br>&#x25A0;&#x25A0;',             // J
            '&nbsp;&#x25A0;<br>&nbsp;&#x25A0;<br>&#x25A0;&#x25A0;', // L
            '&#x25A0;&#x25A0;<br>&#x25A0;&#x25A0;',                 // Square
            '&nbsp;&#x25A0;<br>&#x25A0;&#x25A0;&#x25A0;',           // T
            '&#x25A0;&#x25A0;<br>&nbsp;&#x25A0;&#x25A0;',           // Z
            '&nbsp;&#x25A0;&#x25A0;<br>&#x25A0;&#x25A0;'            // S
        ]);
    }
}
