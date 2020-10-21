<?php

namespace Tests\Unit;

use Mrchimp\Chimpcom\Booleanate;
use Tests\TestCase;

class BooleanateTest extends TestCase
{
    /** @test */
    public function can_accept_positive_words()
    {
        $this->assertTrue(Booleanate::isAffirmative('yes'));
        $this->assertTrue(Booleanate::isAffirmative('ok'));
        $this->assertTrue(Booleanate::isAffirmative('y'));
        $this->assertFalse(Booleanate::isNegative('yes'));
        $this->assertFalse(Booleanate::isNegative('ok'));
        $this->assertFalse(Booleanate::isNegative('y'));
    }

    /** @test */
    public function can_accept_negative_words()
    {
        $this->assertTrue(Booleanate::isNegative('no'));
        $this->assertTrue(Booleanate::isNegative('n'));
        $this->assertTrue(Booleanate::isNegative('abort'));
        $this->assertFalse(Booleanate::isAffirmative('no'));
        $this->assertFalse(Booleanate::isAffirmative('n'));
        $this->assertFalse(Booleanate::isAffirmative('abort'));
    }
}
