<?php

namespace Tests\Unit;

use Mrchimp\Chimpcom\Filesystem\Path;
use Tests\TestCase;

class PathTest extends TestCase
{
    /** @test */
    public function path_is_a_thing()
    {
        $path = Path::make('/parent/child/grandchild');

        $this->assertEquals('parent', $path->first());
        $this->assertEquals('parent', $path->get());
        $this->assertEquals('child', $path->next());
        $this->assertEquals('grandchild', $path->next());
        $path->reset();
        $this->assertEquals('parent', $path->get());
    }
}
