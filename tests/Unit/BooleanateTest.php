<?php

namespace Tests\Unit;

use Mrchimp\Chimpcom\Booleanate;
use Tests\TestCase;

class BooleanateTest extends TestCase
{
    /** @test */
    public function can_accept_positive_words()
    {
        // dd(Booleanate::isNegative('yes'));
        $this->assertTrue(Booleanate::isAffirmative('yes'));
        $this->assertTrue(Booleanate::isAffirmative('ok'));
        $this->assertTrue(Booleanate::isAffirmative('y'));
        $this->assertFalse(Booleanate::isNegative('yes'));
        $this->assertFalse(Booleanate::isNegative('ok'));
        $this->assertFalse(Booleanate::isNegative('y'));

        $this->assertTrue(Booleanate::isAffirmative('1'));
        $this->assertTrue(Booleanate::isAffirmative(1));
        $this->assertFalse(Booleanate::isNegative('1'));
        $this->assertFalse(Booleanate::isNegative(1));
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

        $this->assertTrue(Booleanate::isNegative('0'));
        $this->assertTrue(Booleanate::isNegative(0));
        $this->assertFalse(Booleanate::isAffirmative('0'));
        $this->assertFalse(Booleanate::isAffirmative(0));
    }
}
