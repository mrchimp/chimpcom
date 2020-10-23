<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Oneliner;
use Tests\TestCase;

class OnelinerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function oneliner_is_not_for_guests()
    {
        $this->getGuestResponse('oneliner command response')
            ->assertSee('You must log in to use this command')
            ->assertStatus(200);
    }

    /** @test */
    public function oneliner_is_not_for_nonadmins()
    {
        $this->getUserResponse('oneliner command response')
            ->assertSee('No.')
            ->assertStatus(200);
    }

    /** @test */
    public function oneliner_creates_a_oneliner_from_command_and_response()
    {
        $this->getAdminResponse('oneliner command response')
            ->assertSee('Ok.')
            ->assertStatus(200);

        $oneliners = Oneliner::all();

        $this->assertCount(1, $oneliners);
        $this->assertEquals('command', $oneliners->first()->command);
        $this->assertEquals('response', $oneliners->first()->response);
    }

    /** @test */
    public function oneliner_response_can_be_multiple_words()
    {
        $this->getAdminResponse('oneliner command Response is multiple words.')
            ->assertSee('Ok.')
            ->assertStatus(200);

        $oneliners = Oneliner::all();

        $this->assertCount(1, $oneliners);
        $this->assertEquals('command', $oneliners->first()->command);
        $this->assertEquals('Response is multiple words.', $oneliners->first()->response);
    }

    /** @test */
    public function multiword_oneliner_response_can_be_quotes()
    {
        $this->getAdminResponse('oneliner command "Response is multiple words."')
            ->assertSee('Ok.')
            ->assertStatus(200);

        $oneliners = Oneliner::all();

        $this->assertCount(1, $oneliners);
        $this->assertEquals('command', $oneliners->first()->command);
        $this->assertEquals('Response is multiple words.', $oneliners->first()->response);
    }

    /** @test */
    public function quoting_oneline_response_allows_for_special_characters()
    {
        $this->getAdminResponse('oneliner command "Response is multiple words. --this-looks-like-an-option"')
            ->assertSee('Ok.')
            ->assertStatus(200);

        $oneliners = Oneliner::all();

        $this->assertCount(1, $oneliners);
        $this->assertEquals('command', $oneliners->first()->command);
        $this->assertEquals('Response is multiple words. --this-looks-like-an-option', $oneliners->first()->response);
    }
}
