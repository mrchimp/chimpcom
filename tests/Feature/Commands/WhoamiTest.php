<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WhoamiTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function whoami_tells_guest_that_they_are_guest()
    {
        $this->getGuestResponse('whoami')
            ->assertSee('Guest')
            ->assertStatus(200);
    }

    /** @test */
    public function whoami_tells_users_their_name()
    {
        $user = User::factory()->create([
            'name' => 'fred',
        ]);

        $this->getUserResponse('whoami', $user)
            ->assertSee('fred')
            ->assertStatus(200);
    }
}
