<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Feed;
use Mrchimp\Chimpcom\Models\Memory;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function stats_command_gets_a_list_stats()
    {
        factory(User::class, 2)->create();
        factory(Memory::class, 3)->create();
        factory(Feed::class, 4)->create();

        $this->getGuestResponse('stats')
            ->assertSee('Users: 2')
            ->assertSee('Memories: 3')
            ->assertSee('Feeds: 4')
            ->assertStatus(200);
    }

    /** @test */
    public function stats_command_can_get_stats_for_single_user()
    {
        $user = factory(User::class)->create([
            'name' => 'fred',
        ]);

        factory(Memory::class, 5)->create([
            'user_id' => $user->id,
        ]);
        factory(Feed::class, 6)->create([
            'user_id' => $user->id,
        ]);

        $this->getGuestResponse('stats fred')
            ->assertSee('Memories: 5')
            ->assertSee('Feeds: 6')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_get_stats_for_nonexistant_user()
    {
        $this->getGuestResponse('stats santa')
            ->assertSee('That username does not exist.')
            ->assertStatus(200);
    }
}
