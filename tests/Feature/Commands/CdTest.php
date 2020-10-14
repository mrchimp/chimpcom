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
        $this->user = factory(User::class)->create([
            'name' => 'fred',
        ]);

        $this->home = factory(Directory::class)->create([
            'name' => 'home',
            'owner_id' => $this->user->id,
        ]);
        $this->fred = factory(Directory::class)->create([
            'name' => 'fred',
            'owner_id' => $this->user->id,
        ]);
        $this->bin = factory(Directory::class)->create([
            'name' => 'bin',
            'owner_id' => $this->user->id,
        ]);

        $this->file = factory(File::class)->create([
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
    }
}
