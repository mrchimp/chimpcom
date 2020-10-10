<?php

namespace Tests\Unit;

use App\User;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
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

    /** @test */
    public function fullPath_gets_the_full_path_as_you_would_expect()
    {
        $root = factory(Directory::class)->create([
            'name' => 'root',
        ]);
        $child = factory(Directory::class)->create([
            'name' => 'child',
        ]);
        $other_child = factory(Directory::class)->create([
            'name' => 'other_child',
        ]);

        $grandchild = factory(Directory::class)->create([
            'name' => 'grandchild',
        ]);
        $other_grandchild = factory(Directory::class)->create([
            'name' => 'other_grandchild',
        ]);

        $root->appendNode($child);
        $root->appendNode($other_child);
        $child->appendNode($grandchild);
        $child->appendNode($other_grandchild);

        $root->refresh();
        $child->refresh();
        $grandchild->refresh();

        $this->assertEquals('/', $root->fullPath());
        $this->assertEquals('/child', $child->fullPath());
        $this->assertEquals('/child/grandchild', $grandchild->fullPath());
    }
}
