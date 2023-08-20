<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Filesystem\Path;
use Mrchimp\Chimpcom\Models\Directory;
use Tests\TestCase;

class Register3Test extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->getGuestResponse('register fred');
        $this->getGuestResponse('hunter22', $this->last_action_id);
        $this->getGuestResponse('hunter22', $this->last_action_id);
    }

    /** @test */
    public function providing_email_address_completes_registration()
    {
        Directory::factory()->create(['name' => 'home']);

        $this->getGuestResponse('fred@example.com', $this->last_action_id)
            ->assertOk()
            ->assertSee('Hello, fred! Welcome to Chimpcom.');
        $this->assertNoAction();

        $this->assertTrue(Auth::check());
        $this->assertTrue(Path::make('/home/fred')->exists());
    }

    /** @test */
    public function register3_email_must_be_valid()
    {
        $this->getGuestResponse('asdffdasfdsaafds', $this->last_action_id)
            ->assertSee('Something went wrong. Please try again.')
            ->assertOk();
        $this->assertNoAction();
    }
}
