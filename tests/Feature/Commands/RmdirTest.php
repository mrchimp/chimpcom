<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class Rmdirtest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_user_rmdir()
    {
        $this->getGuestResponse('rmdir dirname')
            ->assertSee('You must log in to use this command');
    }

    /** @test */
    public function users_can_remove_directories_they_own()
    {
        $user = factory(User::class)->create();

        $parent_dir = factory(Directory::class)->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = factory(Directory::class)->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $parent_dir->appendNode($child_dir);

        $user->currentDirectory()->associate($parent_dir);

        $this->getUserResponse('rmdir child_dir', $user)
            ->assertStatus(200)
            ->assertSee('Ok.');

        $directories = Directory::all();

        $this->assertCount(1, $directories);
        $this->assertEquals('parent_dir', $directories->first()->name);
    }

    /** @test */
    public function admin_can_remove_other_users_directories()
    {
        $user = factory(User::class)->create();
        $admin = factory(User::class)->states('admin')->create();

        $parent_dir = factory(Directory::class)->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = factory(Directory::class)->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $parent_dir->appendNode($child_dir);

        $admin->currentDirectory()->associate($parent_dir);

        $this->getAdminResponse('rmdir child_dir', $admin)
            ->assertStatus(200)
            ->assertSee('Ok');

        $directories = Directory::all();

        $this->assertCount(1, $directories);
        $this->assertEquals('parent_dir', $directories->first()->name);
    }

    /** @test */
    public function cant_remove_directory_if_it_contains_a_subdirectory()
    {
        $user = factory(User::class)->create();

        $parent_dir = factory(Directory::class)->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = factory(Directory::class)->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $grandchild_dir = factory(Directory::class)->create([
            'name' => 'grandchild_dir',
            'owner_id' => $user->id,
        ]);
        $parent_dir->appendNode($child_dir);
        $child_dir->appendNode($grandchild_dir);

        $user->currentDirectory()->associate($parent_dir);

        $this->getUserResponse('rmdir child_dir', $user)
            ->assertStatus(200)
            ->assertSee('That directory is not empty.');

        $directories = Directory::all();

        $this->assertCount(3, $directories);
    }

    /** @test */
    public function cant_remove_directory_if_it_contains_a_file()
    {
        $user = factory(User::class)->create();

        $parent_dir = factory(Directory::class)->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = factory(Directory::class)->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $file = factory(File::class)->create([
            'name' => 'file',
        ]);
        $parent_dir->appendNode($child_dir);
        $child_dir->files()->save($file);

        $user->currentDirectory()->associate($parent_dir);

        $this->getUserResponse('rmdir child_dir', $user)
            ->assertStatus(200)
            ->assertSee('That directory is not empty.');

        $directories = Directory::all();
        $files = File::all();

        $this->assertCount(2, $directories);
        $this->assertCount(1, $files);
    }
}
