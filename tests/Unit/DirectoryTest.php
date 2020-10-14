<?php

namespace Tests\Unit;

use App\User;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Filesystem\RootDirectory;
use Mrchimp\Chimpcom\Models\Directory;
use Tests\TestCase;

class DirectoryTest extends TestCase
{
    /** @test */
    public function users_can_have_a_current_directory()
    {
        $directory = factory(Directory::class)->create();
        $user = factory(User::class)->create();

        $user->currentDirectory()->associate($directory)->save();

        $user = User::with('currentDirectory')->first();

        $this->assertEquals($directory->id, Directory::current($user)->id);
        $this->assertInstanceOf(Directory::class, Directory::current($user));
    }

    /** @test */
    public function if_user_has_no_current_null_is_returned()
    {
        $user = factory(User::class)->create();

        $this->assertInstanceOf(RootDirectory::class, Directory::current($user));
        $this->assertEquals('/', Directory::current($user)->fullPath());
        $this->assertEquals('/', Directory::current($user)->name);
    }

    /** @test */
    public function directories_can_have_parent_child_relationship()
    {
        $parent = factory(Directory::class)->create();
        $child_1 = factory(Directory::class)->create();
        $child_2 = factory(Directory::class)->create();

        $parent->appendNode($child_1);
        $parent->appendNode($child_2);

        $this->assertCount(2, $parent->descendants);
        $this->assertTrue($child_1->isDescendantOf($parent));
        $this->assertTrue($child_2->isDescendantOf($parent));
    }

    /** @test */
    public function fullPath_gets_the_full_path_as_you_would_expect()
    {
        $directory = factory(Directory::class)->create([
            'name' => 'directory',
        ]);

        $child = factory(Directory::class)->create([
            'name' => 'child',
        ]);

        $directory->appendNode($child);

        $directory->refresh();
        $child->refresh();

        $this->assertEquals('/directory', $directory->fullPath());
        $this->assertEquals('/directory/child', $child->fullPath());
    }
}
