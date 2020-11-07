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
        $this->getGuestResponse('hunter22');
        $this->getGuestResponse('hunter22');
    }

    /** @test */
    public function providing_email_address_completes_registration()
    {
        Directory::factory()->create(['name' => 'home']);

        $this->getGuestResponse('fred@example.com')
            ->assertStatus(200)
            ->assertSee('Hello, fred! Welcome to Chimpcom.')
            ->assertSessionMissing('register_username')
            ->assertSessionMissing('register_password')
            ->assertSessionMissing('register_password2')
            ->assertSessionHas('action', 'normal');

        $this->assertTrue(Auth::check());
        $this->assertTrue(Path::make('/home/fred')->exists());
    }

    /** @test */
    public function register3_email_must_be_valid()
    {
        $this->getGuestResponse('asdffdasfdsaafds')
            ->assertSee('Something went wrong. Please try again.')
            ->assertStatus(200)
            ->assertSessionHas('action', 'normal')
            ->assertSessionMissing('register_username')
            ->assertSessionMissing('register_password')
            ->assertSessionMissing('register_password2');
    }
}
