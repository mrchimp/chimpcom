<?php

namespace Tests\Unit;

use App\User;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Filesystem\Path;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class PathTest extends TestCase
{
    /** @test */
    public function path_allows_traversing_via_segment()
    {
        $this->root = factory(Directory::class)->create([
            'name' => 'root',
        ]);
        $this->parent = factory(Directory::class)->create([
            'name' => 'parent',
        ]);
        $this->child = factory(Directory::class)->create([
            'name' => 'child',
        ]);
        $this->grandchild = factory(Directory::class)->create([
            'name' => 'grandchild',
        ]);

        $this->root->appendNode($this->parent);
        $this->parent->appendNode($this->child);
        $this->child->appendNode($this->grandchild);

        $directory = factory(Directory::class)->create();

        $path = Path::make('/parent/child/grandchild', $directory);

        $this->assertEquals('parent', $path->first());
        $this->assertEquals('parent', $path->get());
        $this->assertEquals('child', $path->next());
        $this->assertEquals('grandchild', $path->next());
        $path->reset();
        $this->assertEquals('parent', $path->get());
    }

    /** @test */
    public function can_get_a_directory_from_a_path()
    {
        $path_str = '/home/test';

        $root = factory(Directory::class)->create([
            'name' => 'root',
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
        ]);
        $test = factory(Directory::class)->create([
            'name' => 'test',
        ]);

        $root->appendNode($home);
        $home->appendNode($test);

        $path = Path::make($path_str);

        $this->assertTrue($path->isDirectory());
        $this->assertTrue($path->exists());
        $this->assertInstanceOf(Directory::class, $path->target());
        $this->assertEquals('home', $path->parent()->name);
    }

    /** @test */
    public function if_path_does_not_exist_then_path_object_knows_that()
    {
        $path_str = '/home/test';

        $root = factory(Directory::class)->create([
            'name' => 'root',
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
        ]);

        $root->appendNode($home);

        // $this->expectException(InvalidPathException::class);

        $path = Path::make($path_str);

        $this->assertFalse($path->exists());
    }

    /** @test */
    public function can_get_a_file_from_a_path()
    {
        $path_str = '/home/test/file';

        $root = factory(Directory::class)->create([
            'name' => 'root',
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
        ]);
        $test = factory(Directory::class)->create([
            'name' => 'test',
        ]);
        $file = factory(File::class)->create([
            'name' => 'file',
        ]);

        $root->appendNode($home);
        $home->appendNode($test);
        $test->files()->save($file);

        $path = Path::make($path_str);

        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        $this->assertInstanceOf(File::class, $path->target());
        $this->assertEquals('file', $path->target()->name);
        $this->assertEquals('test', $path->parent()->name);
    }

    /** @test */
    public function paths_that_are_too_long_cause_an_exception()
    {
        $this->expectException(InvalidPathException::class);
        Path::make('a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a');
    }

    /** @test */
    public function paths_that_go_above_root_throw_an_exception()
    {
        $directory = factory(Directory::class)->create([
            'name' => 'directory',
        ]);
        $subdirectory = factory(Directory::class)->create([
            'name' => 'subdirectory',
        ]);
        $directory->appendNode($subdirectory);
        $this->expectException(InvalidPathException::class);
        Path::make('../..', $subdirectory);
    }

    /** @test */
    public function paths_that_point_to_something_that_doesnt_exist_do_just_that()
    {
        // Create a directory to avoid "filesystem unavailable" error
        factory(Directory::class)->create();

        $path_1 = Path::make('/file_that_does_not_exist');
        $this->assertFalse($path_1->exists());

        $path_2 = Path::make('file_that_does_not_exist');
        $this->assertFalse($path_2->exists());
    }

    /** @test */
    public function if_a_path_doesnt_exist_but_its_parent_directory_does_then_we_can_get_that()
    {
        $root = factory(Directory::class)->create([
            'name' => 'root'
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
        ]);
        $fred = factory(Directory::class)->create([
            'name' => 'fred',
        ]);

        $root->appendNode($home);
        $home->appendNode($fred);

        $path = Path::make('/home/fred/doesntexist');

        $this->assertFalse($path->exists());
        $this->assertInstanceOf(Directory::class, $path->parent());
        $this->assertEquals('fred', $path->parent()->name);
    }

    /** @test */
    public function a_directory_can_be_created_in_the_parent_directory()
    {
        $user = factory(User::class)->create();
        $root = factory(Directory::class)->create([
            'name' => 'root',
            'owner_id' => $user->id,
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
            'owner_id' => $user->id,
        ]);
        $root->appendNode($home);

        $path = Path::make('/home/fred');

        $this->assertFalse($path->exists());
        $path->makeDirectory($user, 'fred');
        $path->resolve();
        $this->assertTrue($path->exists());
        $this->assertInstanceOf(Directory::class, $path->target());
        $this->assertEquals('fred', $path->target()->name);
    }

    /** @test */
    public function a_file_can_be_created_in_the_parent_directory()
    {
        $user = factory(User::class)->create();
        $root = factory(Directory::class)->create([
            'name' => 'root',
            'owner_id' => $user->id,
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
            'owner_id' => $user->id,
        ]);
        $root->appendNode($home);

        $path = Path::make('/home/fred');

        $this->assertFalse($path->exists());
        $path->makeFile($user, 'fred');
        $path->resolve();
        $this->assertTrue($path->exists());
        $this->assertInstanceOf(File::class, $path->target());
        $this->assertEquals('fred', $path->target()->name);
    }
}
