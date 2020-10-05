<?php

namespace Tests\Unit;

use App\User;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class FileTest extends TestCase
{
    /** @test */
    public function files_can_be_put_in_directories()
    {
        $file = factory(File::class)->create([
            'name' => 'myfile',
        ]);

        $directory = factory(Directory::class)->create([
            'name' => 'my_dir',
        ]);

        $directory->files()->save($file);

        $directory->refresh();

        $this->assertCount(1, $directory->files);
    }

    /** @test */
    public function file_can_have_an_owner_and_can_provide_an_owner_name()
    {
        $file = factory(File::class)->create();

        $this->assertEquals('root', $file->ownerName());

        $user = factory(User::class)->create([
            'name' => 'Some User',
        ]);

        $file->owner()->associate($user);

        $file->refresh();

        $this->assertEquals('Some User', $file->ownerName());
    }
}
