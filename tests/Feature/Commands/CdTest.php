<?php

namespace Tests\Feature\Commands;

use App\User;
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

        $this->root = factory(Directory::class)->create([
            'name' => 'root',
            'owner_id' => $this->user->id,
        ]);
        $this->home = factory(Directory::class)->create([
            'name' => 'home',
            'owner_id' => $this->user->id,
        ]);
        $this->fred = factory(Directory::class)->create([
            'name' => 'fred',
            'owner_id' => $this->user->id,
        ]);

        $this->file = factory(File::class)->create([
            'name' => 'file',
            'owner_id' => $this->user->id,
        ]);

        $this->root->appendNode($this->home);
        $this->home->appendNode($this->fred);

        $this->fred->files()->save($this->file);
    }

    /** @test */
    public function cd_without_a_target_takes_user_home()
    {
        $this->makeDirStructure();

        $this->root->setCurrent($this->user);

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
        $this->getUserResponse('cd /', $this->user);
        $this->assertEquals($this->root->name, Directory::current($this->user)->name);

        // Go to absolute path
        $this->getUserResponse('cd /home', $this->user);
        // dump($response->getContent());
        $this->assertEquals($this->home->name, Directory::current($this->user)->name);

        // Go up a directory
        $this->getUserResponse('cd ..', $this->user);
        $this->assertEquals($this->root->name, Directory::current($this->user)->name);

        // Go to absolute nested path
        $this->getUserResponse('cd /home/fred', $this->user);
        $this->assertEquals($this->fred->name, Directory::current($this->user)->name);

        // Go to current directory
        $this->getUserResponse('cd .', $this->user);
        $this->assertEquals($this->fred->name, Directory::current($this->user)->name);

        // Go up two directories
        $this->getUserResponse('cd ../..', $this->user);
        $this->assertEquals($this->root->name, Directory::current($this->user)->name);

        // Go to a relative path
        $this->getUserResponse('cd home');
        $this->assertEquals($this->home->name, Directory::current($this->user)->name);

        // Combined different dot path parts
        $this->getUserResponse('cd ./..');
        $this->assertEquals($this->root->name, Directory::current($this->user)->name);

        // Go to relative nested path
        $this->getUserResponse('cd home/fred', $this->user);
        $this->assertEquals($this->fred->name, Directory::current($this->user)->name);

        // Go back to root
        $this->getUserResponse('cd /', $this->user);
        $this->assertEquals($this->root->name, Directory::current($this->user)->name);
    }
}
