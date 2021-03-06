<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Filesystem\RootDirectory;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class CdTest extends TestCase
{
    protected function makeDirStructure()
    {
        $this->user = User::factory()->create([
            'name' => 'fred',
        ]);

        $this->home = Directory::factory()->create([
            'name' => 'home',
            'owner_id' => $this->user->id,
        ]);
        $this->fred = Directory::factory()->create([
            'name' => 'fred',
            'owner_id' => $this->user->id,
        ]);
        $this->bin = Directory::factory()->create([
            'name' => 'bin',
            'owner_id' => $this->user->id,
        ]);

        $this->file = File::factory()->create([
            'name' => 'file',
            'owner_id' => $this->user->id,
        ]);

        $this->home->appendNode($this->fred);

        $this->fred->files()->save($this->file);
    }

    /** @test */
    public function cd_without_a_target_takes_user_home()
    {
        $this->makeDirStructure();

        $this->bin->setCurrent($this->user);

        $this->getUserResponse('cd', $this->user)
            ->assertStatus(200);

        $current = Directory::current($this->user);

        $this->assertEquals($this->fred->name, $current->name);
    }

    /** @test */
    public function can_cd_to_many_places()
    {
        $this->makeDirStructure();

        // Go to root
        $this->getUserResponse('cd /', $this->user)->assertStatus(200);
        $this->assertEquals('/', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_to_an_absolute_path()
    {
        $this->makeDirStructure();

        (new RootDirectory)->setCurrent($this->user);

        $this->getUserResponse('cd /home', $this->user)->assertStatus(200);
        $this->assertEquals('home', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_up_one_directory()
    {
        $this->makeDirStructure();

        $this->home->setCurrent($this->user);

        $this->getUserResponse('cd ..', $this->user)->assertStatus(200);
        $this->assertEquals('/', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_to_an_absolute_path_with_multiple_levels()
    {
        $this->makeDirStructure();

        (new RootDirectory)->setCurrent($this->user);

        $this->getUserResponse('cd /home/fred', $this->user)->assertStatus(200);
        $this->assertEquals('fred', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_to_current_directory()
    {
        $this->makeDirStructure();

        $this->fred->setCurrent($this->user);

        $this->getUserResponse('cd .', $this->user)->assertStatus(200);
        $this->assertEquals('fred', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_up_multiple_levels()
    {
        $this->makeDirStructure();

        $this->fred->setCurrent($this->user);

        $this->getUserResponse('cd ../..', $this->user)->assertStatus(200);
        $this->assertEquals('/', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_to_a_relative_path()
    {
        $this->makeDirStructure();

        (new RootDirectory)->setCurrent($this->user);

        $this->getUserResponse('cd home')->assertStatus(200);
        $this->assertEquals('home', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_to_a_path_with_different_dot_path_types()
    {
        $this->makeDirStructure();

        $this->home->setCurrent($this->user);

        $this->getUserResponse('cd ./..')->assertStatus(200);
        $this->assertEquals('/', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_to_a_relative_path_with_multiple_segments()
    {
        $this->makeDirStructure();

        (new RootDirectory)->setCurrent($this->user);

        $this->getUserResponse('cd home/fred', $this->user)->assertStatus(200);
        $this->assertEquals('fred', Directory::current($this->user)->name);
    }

    /** @test */
    public function can_cd_to_root()
    {
        $this->makeDirStructure();

        $this->fred->setCurrent($this->user);

        $this->getUserResponse('cd /', $this->user)->assertStatus(200);
        $this->assertEquals('/', Directory::current($this->user)->name);
        $this->assertInstanceOf(RootDirectory::class, Directory::current($this->user));
    }

    /** @test */
    public function cd_penguin_gives_a_joke_answer()
    {
        $this->getGuestResponse('cd penguin')
            ->assertStatus(200)
            ->assertSee('You are inside a penguin. It is dark.');
    }

    /** @test */
    public function you_can_cd_to_a_dir_called_penguin()
    {
        $user = User::factory()->create();

        Directory::factory()->create([
            'name' => 'penguin',
        ]);

        (new RootDirectory)->setCurrent($this->user);

        $this->getUserResponse('cd penguin', $user)
            ->assertStatus(200)
            ->assertSee('You go.');

        $this->assertEquals('penguin', Directory::current($this->user)->name);
    }

    /** @test */
    public function cd_c_gives_a_joke_answer()
    {
        $this->getGuestResponse('cd c:')
            ->assertStatus(200)
            ->assertSee('What d\'you think this is, Windows?');

        $this->getGuestResponse('cd C:')
            ->assertStatus(200)
            ->assertSee('What d\'you think this is, Windows?');
    }

    /** @test */
    public function cding_to_invalid_paths_fail()
    {
        $this->getGuestResponse('cd /a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/')
            ->assertStatus(200)
            ->assertSee('Path length is too long.');
    }

    /** @test */
    public function cding_to_a_directory_that_doesnt_exist_fails()
    {
        $this->getGuestResponse('cd wherever')
            ->assertStatus(200)
            ->assertSee('No such file or directory');
    }

    /** @test */
    public function you_cant_cd_to_a_file()
    {
        $this->makeDirStructure();

        $this->getGuestResponse('cd /home/fred/file')
            ->assertStatus(200)
            ->assertSee('Target is not a directory.');
    }

    /** @test */
    public function can_get_tab_completions_in_current_directory()
    {
        $this->makeDirStructure();

        (new RootDirectory)->setCurrent($this->user);

        $json = $this->actingAs($this->user)
            ->get('/ajax/tabcomplete?cmd_in=cd h')
            ->assertStatus(200)
            ->json();

        $this->assertCount(1, $json);
        $this->assertEquals('cd home', $json[0]);
    }

    /** @test */
    public function if_there_are_no_autocompletions_then_you_dont_get_any_autocompletions_ok()
    {
        $this->makeDirStructure();

        (new RootDirectory)->setCurrent($this->user);

        $json = $this->actingAs($this->user)
            ->get('/ajax/tabcomplete?cmd_in=cd asdasdsad')
            ->assertStatus(200)
            ->json();

        $this->assertCount(0, $json);
    }
}
