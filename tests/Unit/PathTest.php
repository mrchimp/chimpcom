<?php

namespace Tests\Unit;

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
    }

    /** @test */
    public function if_path_does_not_exist_then_exception_is_thrown()
    {
        $path_str = '/home/test';

        $root = factory(Directory::class)->create([
            'name' => 'root',
        ]);
        $home = factory(Directory::class)->create([
            'name' => 'home',
        ]);

        $root->appendNode($home);

        $this->expectException(InvalidPathException::class);

        Path::make($path_str);
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
    }

    /** @test */
    public function invalid_paths_cause_exceptions()
    {
        $this->expectException(InvalidPathException::class);
        Path::make('/file_that_does_not_exist');

        $this->expectException(InvalidPathException::class);
        Path::make('file_that_does_not_exist');

        $this->expectException(InvalidPathException::class);
        Path::make('../..', $this->home);
    }
}
