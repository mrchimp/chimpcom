<?php

namespace Tests\Feature\Commands;

use App\User;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class EditTest extends TestCase
{
    /** @test */
    public function edit_command_is_not_for_guest_peasants_get_an_account_noob()
    {
        $this->getGuestResponse('edit file')
            ->assertStatus(200)
            ->assertSee('You must log in to use this command.');
    }

    /** @test */
    public function edit_command_fails_if_file_does_not_exist()
    {
        $this->getUserResponse('edit file')
            ->assertStatus(200)
            ->assertSee('File does not exist.');
    }

    /** @test */
    public function edit_fails_if_user_doesnt_own_the_file()
    {
        $other_user = factory(User::class)->create();
        $user = factory(User::class)->create();
        $directory = factory(Directory::class)->create();
        factory(File::class)->create([
            'name' => 'file',
            'owner_id' => $other_user->id,
            'directory_id' => $directory->id,
        ]);
        $directory->setCurrent($user);

        $this->getUserResponse('edit file', $user)
            ->assertStatus(200)
            ->assertSee('You do not own that file.');
    }

    /** @test */
    public function if_all_is_good_then_edit_action_is_set_and_file_contents_is_returned()
    {
        $user = factory(User::class)->create();
        $directory = factory(Directory::class)->create();
        factory(File::class)->create([
            'name' => 'file',
            'owner_id' => $user->id,
            'directory_id' => $directory->id,
            'content' => 'This is the file contents',
        ]);
        $directory->setCurrent($user);

        $json = $this->getUserResponse('edit file', $user)
            ->assertStatus(200)
            ->assertSee('Editing...')
            ->assertSessionHas('action', 'edit')
            ->json();

        $this->assertEquals(
            'This is the file contents',
            $json['edit_content']
        );
    }
}
