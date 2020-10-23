<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Directory;
use Tests\TestCase;

class PwdTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function pwd_shows_current_working_directory()
    {
        $user = factory(User::class)->create([
            'name' => 'fred',
        ]);

        $home = factory(Directory::class)->create([
            'name' => 'home',
            'owner_id' => $user->id,
        ]);
        $fred = factory(Directory::class)->create([
            'name' => 'fred',
            'owner_id' => $user->id,
        ]);

        $home->appendNode($fred);

        $fred->setCurrent($user);

        $this->getUserResponse('pwd', $user)
            ->assertSee('\/home\/fred')
            ->assertStatus(200);
    }
}
