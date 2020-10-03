<?php

namespace Tests\Unit;

use App\User;
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
    public function if_user_has_no_current_directory_one_is_assigned()
    {
        $directory = factory(Directory::class)->create();
        $user = factory(User::class)->create();

        $this->assertEquals($directory->id, Directory::current($user)->id);
        $this->assertInstanceOf(Directory::class, Directory::current($user));
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
}
