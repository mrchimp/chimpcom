<?php

namespace Tests\Feature\Commands;

use App\Mrchimp\Chimpcom\Filesystem\Listers\Bin;
use App\User;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class LsTest extends TestCase
{
    /** @test */
    public function ls_returns_empty_root_dir_listing_if_there_are_no_directories()
    {
        $this->getGuestResponse('ls')
            ->assertStatus(200)
            ->assertSee('Nothing here');
    }

    /** @test */
    public function ls_shows_current_directory_listing_for_guests()
    {
        factory(Directory::class)->create([
            'name' => 'top_level',
        ]);

        $this->getGuestResponse('ls')
            ->assertStatus(200)
            ->assertSee('top_level');
    }

    /** @test */
    public function ls_shows_files_in_directory()
    {
        $user = factory(User::class)->create();
        $dir = factory(Directory::class)->create();
        $file = factory(File::class)->create([
            'name' => 'My File',
        ]);

        $dir->files()->save($file);
        $dir->setCurrent($user);

        $this
            ->getUserResponse('ls', $user)
            ->assertStatus(200)
            ->assertSee('My File');
    }

    /** @test */
    public function ls_shows_list_of_bins_if_theres_a_bin_lister_cos_the_bin_lister_lists_bins()
    {
        $user = factory(User::class)->create([
            'name' => 'Fred Test',
        ]);
        $dir = factory(Directory::class)->create([
            'name' => 'top_level',
            'lister' => Bin::class,
        ]);

        $dir->setCurrent($user);

        $user->refresh();

        $this
            ->getUserResponse('ls', $user)
            ->assertStatus(200)
            ->assertSee('man')
            ->assertSee('login')
            ->assertSee('logout');
    }
}
