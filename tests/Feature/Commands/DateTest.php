<?php

namespace Tests\Feature\Commands;

use Carbon\Carbon;
use Tests\TestCase;

class DateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2020, 10, 30, 13, 02, 03, 'UTC'));
    }

    /** @test */
    public function date_command_returns_the_date()
    {
        $this->getGuestResponse('date')
            ->assertStatus(200)
            ->assertSee('Friday 30th of October 2020 13:02:03 UTC');
    }

    /** @test */
    public function date_command_can_take_date_option()
    {
        $this->getGuestResponse('date --date')
            ->assertStatus(200)
            ->assertSee('Friday 30th of October 2020');
    }

    /** @test */
    public function date_command_can_take_time_option()
    {
        $this->getGuestResponse('date --time')
            ->assertStatus(200)
            ->assertSee('13:02:03');
    }

    /** @test */
    public function date_command_can_return_an_iso_date_string()
    {
        $this->getGuestResponse('date --iso')
            ->assertStatus(200)
            ->assertSee('');
    }
}
