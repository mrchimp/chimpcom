<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TestFormat extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_format_tables()
    {
        $output = \Mrchimp\Chimpcom\Format::listToTable([
            '1', '2',
            '3', '4',
            '5',
        ], 2);

        $this->assertEquals('<table><tr><td>1</td><td>2</td></tr><tr><td>3</td><td>4</td></tr><tr><td>5</td></tr></table>', $output);
    }
}
