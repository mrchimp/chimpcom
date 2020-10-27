<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function users_lists_users()
    {
        $user_1 = factory(User::class)->create();
        $user_2 = factory(User::class)->create();

        $this->getAdminResponse('users')
            ->assertSee($user_1->name)
            ->assertSee($user_2->name)
            ->assertStatus(200);
    }

    /** @test */
    public function users_is_not_for_guests()
    {
        $this->getGuestResponse('users')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertStatus(200);
    }

    /** @test */
    public function users_is_not_for_non_admins()
    {
        $this->getUserResponse('users')
            ->assertSee('No.')
            ->assertStatus(200);
    }
}
