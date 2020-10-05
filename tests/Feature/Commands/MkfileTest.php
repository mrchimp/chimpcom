<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class MkfileTest extends TestCase
{
    /** @test */
    public function guest_cant_use_mkfile()
    {
        $this
            ->getGuestResponse('mkfile makefile')
            ->assertStatus(200)
            ->assertSee('You must log in to use this command.');
    }

    /** @test */
    public function mkfile_creates_a_file_in_current_directory()
    {
        $user = factory(User::class)->create();

        factory(Directory::class)->create([
            'owner_id' => $user->id,
        ]);

        $this
            ->getUserResponse('mkfile my_test_file')
            ->assertStatus(200);

        $file = File::first();

        $this->assertEquals('my_test_file', $file->name);
    }
}
