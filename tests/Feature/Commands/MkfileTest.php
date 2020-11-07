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
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function mkfile_creates_a_file_in_current_directory()
    {
        $user = User::factory()->create();

        $directory = Directory::factory()->create([
            'owner_id' => $user->id,
        ]);

        $directory->setCurrent($user);

        $this
            ->getUserResponse('mkfile my_test_file', $user)
            ->assertStatus(200);

        $file = File::first();

        $this->assertEquals('my_test_file', $file->name);
    }

    /** @test */
    public function cant_create_file_in_a_directory_that_is_not_your_own()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        Directory::factory()->create([
            'owner_id' => $other_user->id,
        ]);

        $this
            ->getUserResponse('mkfile my_test_file', $user)
            ->assertStatus(200)
            ->assertSee('You do not have permission');

        $file_count = File::count();

        $this->assertEquals(0, $file_count);
    }

    /** @test */
    public function cant_create_files_with_same_name_as_dir()
    {
        $user = User::factory()->create();
        $directory = Directory::factory()->create([
            'owner_id' => $user->id,
        ]);
        $child = Directory::factory()->create([
            'name' => 'child',
        ]);
        $directory->appendNode($child);
        $user->currentDirectory()->associate($directory);

        $this->getUserResponse('mkfile child', $user)
            ->assertStatus(422)
            ->assertSee('A directory with that name already exists.');
    }
}
