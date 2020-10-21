<?php

namespace Tests\Unit\Console;

use Mrchimp\Chimpcom\Console\Input;
use Tests\TestCase;

class InputTest extends TestCase
{
    /** @test */
    public function content_can_be_set_and_got()
    {
        $input = new Input('original input');
        $input->setContent('Here is some editable content');

        $this->assertEquals(
            'Here is some editable content',
            $input->getContent()
        );
    }
}
