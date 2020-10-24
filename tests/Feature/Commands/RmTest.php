<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class RmTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->file = factory(File::class)->create([
            'name' => 'testfile',
            'owner_id' => $this->user->id,
        ]);
        $this->dir = factory(Directory::class)->create();
        $this->dir->files()->save($this->file);
        $this->dir->setCurrent($this->user);
    }

    /** @test */
    public function rm_can_remove_a_file()
    {
        $this->getUserResponse('rm testfile', $this->user)
            ->assertSee('Ok.')
            ->assertStatus(200);
    }

    /** @test */
    public function rm_command_is_not_for_guests()
    {
        $this->getGuestResponse('rm testfile')
            ->assertSee('You must log in to use this command.')
            ->assertStatus(200);
    }

    /** @test */
    public function rm_has_a_joke_response_about_penguins()
    {
        $this->getUserResponse('rm penguin')
            ->assertSee('You remove the penguin but another appears in its place.')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_delete_a_file_that_doesnt_exist()
    {
        $this->getUserResponse('rm nonexistantfile', $this->user)
            ->assertSee('File does not exist.')
            ->assertStatus(200);
    }

    /** @test */
    public function cant_delete_other_peoples_files()
    {
        $other_user = factory(User::class)->create();
        $this->dir->setCurrent($other_user);

        $this->getUserResponse('rm testfile', $other_user)
            ->assertSee('You do not own that file.')
            ->assertStatus(200);
    }
}
