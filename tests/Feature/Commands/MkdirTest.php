<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class MkdirTest extends TestCase
{
    /** @test */
    public function guests_cant_use_mkdir()
    {
        $this->getGuestResponse('mkdir dirname')
            ->assertStatus(200)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function user_can_create_dir_within_dir_they_own()
    {
        $user = User::factory()->create();
        $parent_dir = Directory::factory()->create([
            'name' => 'Parent Dir',
            'owner_id' => $user->id,
        ]);
        $user->currentDirectory()->associate($parent_dir);

        $this->getUserResponse('mkdir dirname', $user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $new_dir = Directory::where('name', 'dirname')->first();

        $this->assertInstanceOf(Directory::class, $new_dir);
        $this->assertEquals($user->id, $new_dir->owner_id);
    }

    /** @test */
    public function admins_can_create_dir_within_somebody_elses_directory()
    {
        $regular_user = User::factory()->create();
        $admin_user = User::factory()->admin()->create();
        $parent_dir = Directory::factory()->create([
            'name' => 'Parent Dir',
            'owner_id' => $regular_user->id,
        ]);
        $admin_user->currentDirectory()->associate($parent_dir);

        $this->getAdminResponse('mkdir newdir', $admin_user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $new_dir = Directory::where('name', 'newdir')->first();

        $this->assertInstanceOf(Directory::class, $new_dir);
        $this->assertEquals($admin_user->id, $new_dir->owner_id);
    }

    /** @test */
    public function directory_names_get_sanitised_when_creating_them()
    {
        $user = User::factory()->create();
        $parent_dir = Directory::factory()->create([
            'name' => 'Parent Dir',
            'owner_id' => $user->id,
        ]);
        $user->currentDirectory()->associate($parent_dir);

        $this->getUserResponse('mkdir dir/name@/\\with_special-chars=+][}{', $user)
            ->assertSee('Ok.')
            ->assertStatus(200);

        $new_dir = Directory::where('name', 'dirname-at-with-special-chars')->first();

        $this->assertInstanceOf(Directory::class, $new_dir);
        $this->assertEquals($user->id, $new_dir->owner_id);
    }

    /** @test */
    public function mkdir_fails_if_there_are_no_directories_at_all()
    {
        $this->getUserResponse('mkdir dirname')
            ->assertStatus(200)
            ->assertSee('You do not have permission to create a directory here');
    }

    /** @test */
    public function cant_create_files_with_same_name_as_directories()
    {
        $user = User::factory()->create();
        $directory = Directory::factory()->create([
            'name' => 'Parent Dir',
            'owner_id' => $user->id,
        ]);

        $file = File::factory()->create([
            'name' => 'file',
        ]);
        $directory->files()->save($file);
        $user->currentDirectory()->associate($directory);

        $this->getUserResponse('mkdir file', $user)
            ->assertStatus(422)
            ->assertSee('A file with that name already exists.');
    }
}
