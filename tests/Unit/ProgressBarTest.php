<?php

namespace Tests\Unit;

use App\Mrchimp\Chimpcom\ProgressBar;
use Tests\TestCase;

class ProgressBarTest extends TestCase
{
    /** @test */
    public function progress_bar_can_be_created()
    {
        $bar = ProgressBar::make(3, 5);

        $this->assertEquals('▰▰▰▰▰▰<span class="grey_text">▱▱▱▱</span>', $bar->toString());
    }

    /** @test */
    public function width_can_be_controlled()
    {
        $bar = ProgressBar::make(3, 6);

        $this->assertEquals('▰▰▰▰▰▰<span class="grey_text">▱▱▱▱▱▱</span>', $bar->toString(12));
    }
        /** @test */
    public function characters_can_be_controlled()
    {
        $bar = ProgressBar::make(3, 5);

        $this->assertEquals('===<span class="grey_text">--</span>', $bar->toString(5, '=', '-'));
    }

    /** @test */
    public function if_total_is_zero_then_output_empty_bar()
    {
        $bar = ProgressBar::make(3, 0);

        $this->assertEquals('<span class="grey_text">▱▱▱▱</span>', $bar->toString(4));
    }
}
