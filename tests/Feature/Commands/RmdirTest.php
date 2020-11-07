<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class RmdirTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_user_rmdir()
    {
        $this->getGuestResponse('rmdir dirname')
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function users_can_remove_directories_they_own()
    {
        $user = User::factory()->create();

        $parent_dir = Directory::factory()->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = Directory::factory()->create([
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
    public function users_cant_remove_directories_that_belong_to_another_user()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        $parent_dir = Directory::factory()->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = Directory::factory()->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $parent_dir->appendNode($child_dir);

        $other_user->currentDirectory()->associate($parent_dir);

        $this->getUserResponse('rmdir child_dir', $other_user)
            ->assertStatus(200)
            ->assertSee('You do not have permission to remove that directory');

        $directories = Directory::all();

        $this->assertCount(2, $directories);
    }

    /** @test */
    public function admin_can_remove_other_users_directories()
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $parent_dir = Directory::factory()->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = Directory::factory()->create([
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
        $user = User::factory()->create();

        $parent_dir = Directory::factory()->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = Directory::factory()->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $grandchild_dir = Directory::factory()->create([
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
        $user = User::factory()->create();

        $parent_dir = Directory::factory()->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = Directory::factory()->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $file = File::factory()->create([
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

    /** @test */
    public function penguins_can_be_removed_but_will_respawn()
    {
        $this->getUserResponse('rmdir penguin')
            ->assertSee('You remove the penguin but another appears in its place.')
            ->assertStatus(200);
    }

    /** @test */
    public function users_cant_remove_directories_that_dont_exist()
    {
        $user = User::factory()->create();

        $parent_dir = Directory::factory()->create([
            'name' => 'parent_dir',
            'owner_id' => $user->id,
        ]);
        $child_dir = Directory::factory()->create([
            'name' => 'child_dir',
            'owner_id' => $user->id,
        ]);
        $parent_dir->appendNode($child_dir);

        $user->currentDirectory()->associate($parent_dir);

        $this->getUserResponse('rmdir some_other_dir', $user)
            ->assertStatus(200)
            ->assertSee('Directory does not exist.');

        $directories = Directory::all();

        $this->assertCount(2, $directories);
    }
}
