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
        $file = File::factory()->create([
            'name' => 'myfile',
        ]);

        $directory = Directory::factory()->create([
            'name' => 'my_dir',
        ]);

        $directory->files()->save($file);

        $directory->refresh();

        $this->assertCount(1, $directory->files);
    }

    /** @test */
    public function file_can_have_an_owner_and_can_provide_an_owner_name()
    {
        $file = File::factory()->create();

        $this->assertEquals('root', $file->ownerName());

        $user = User::factory()->create([
            'name' => 'Some User',
        ]);

        $file->owner()->associate($user);

        $file->refresh();

        $this->assertEquals('Some User', $file->ownerName());
    }
}
