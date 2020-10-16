<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class CatTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_is_not_available_to_guests()
    {
        $this->getGuestResponse('cat file')
            ->assertSee('You must log in to use this command')
            ->assertStatus(200);
    }

    /** @test */
    public function cat_can_show_a_files_contents()
    {
        $this->user = factory(User::class)->create();

        $directory = factory(Directory::class)->create([
            'owner_id' => $this->user->id,
        ]);

        $directory->setCurrent($this->user);

        factory(File::class)->create([
            'name' => 'test_file',
            'directory_id' => $directory->id,
            'content' => 'file contents',
            'owner_id' => $this->user->id,
        ]);

        $this->getUserResponse('cat test_file')
            ->assertSee('file contents')
            ->assertStatus(200);
    }

    /** @test */
    public function if_a_file_doesnt_exists_you_cant_read_it_i_think_thats_fair()
    {
        $this->user = factory(User::class)->create();

        $directory = factory(Directory::class)->create([
            'owner_id' => $this->user->id,
        ]);

        $directory->setCurrent($this->user);

        $this->getUserResponse('cat file_that_doesnt_exist')
            ->assertSee('File does not exist')
            ->assertStatus(200);
    }

    /** @test */
    public function cannot_read_other_peoples_files()
    {
        $this->user = factory(User::class)->create();
        $this->otheruser = factory(User::class)->create();

        $directory = factory(Directory::class)->create([
            'owner_id' => $this->otheruser->id,
        ]);

        factory(File::class)->create([
            'name' => 'test_file',
            'directory_id' => $directory->id,
            'content' => 'file contents',
            'owner_id' => $this->otheruser->id,
        ]);

        $directory->setCurrent($this->user);

        $this->getUserResponse('cat test_file')
            ->assertSee('You do not own that file')
            ->assertStatus(200);
    }

    /** @test */
    public function cat_will_render_markdown_as_html()
    {
        $this->user = factory(User::class)->create();

        $directory = factory(Directory::class)->create([
            'owner_id' => $this->user->id,
        ]);

        $directory->setCurrent($this->user);

        factory(File::class)->create([
            'name' => 'test_file',
            'directory_id' => $directory->id,
            'content' => '# This is a title',
            'owner_id' => $this->user->id,
        ]);

        $json = $this->getUserResponse('cat test_file')
            ->assertStatus(200)
            ->json();

        $this->assertStringContainsString(
            '<div class="blue_highlight"># This is a title</div>',
            $json['cmd_out']
        );
    }
}
