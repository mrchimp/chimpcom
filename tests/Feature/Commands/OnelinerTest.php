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
            ->assertSee(__('chimpcom.must_log_in'))
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

    /** @test */
    public function duplicate_oneliners_cant_be_created_without_flag()
    {
        Oneliner::factory()->create([
            'command' => 'foo',
            'response' => 'bar'
        ]);

        $this->getAdminResponse('oneliner foo bar')
            ->assertSee('These oneliners already exist.')
            ->assertSee('Use --force to create another.')
            ->assertSee('foo')
            ->assertSee('bar');

        $this->assertEquals(1, Oneliner::count());
    }

    /** @test */
    public function duplicate_oneliners_can_be_created_with_the_force_flag()
    {
        Oneliner::factory()->create([
            'command' => 'foo',
            'response' => 'bar'
        ]);

        $this->getAdminResponse('oneliner --force foo bar')
            ->assertSee('Ok');

        $this->assertEquals(2, Oneliner::count());
    }
}
