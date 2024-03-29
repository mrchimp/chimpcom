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
            ->assertSee(__('chimpcom.must_log_in'));
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
        $other_user = User::factory()->create();
        $user = User::factory()->create();
        $directory = Directory::factory()->create();
        File::factory()->create([
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
        $user = User::factory()->create();
        $directory = Directory::factory()->create();
        File::factory()->create([
            'name' => 'file',
            'owner_id' => $user->id,
            'directory_id' => $directory->id,
            'content' => 'This is the file contents',
        ]);
        $directory->setCurrent($user);

        $json = $this->getUserResponse('edit file', $user)
            ->assertStatus(200)
            ->assertSee('Editing...')
            ->json();

        $this->assertAction('edit');

        $this->assertEquals(
            'This is the file contents',
            $json['edit_content']
        );
    }

    /** @test */
    public function if_edit_file_goes_away_while_its_being_edited_then_that_would_suck_wouldnt_it()
    {
        $user = User::factory()->create();
        $directory = Directory::factory()->create();
        $file = File::factory()->create([
            'name' => 'file',
            'owner_id' => $user->id,
            'directory_id' => $directory->id,
            'content' => 'This is the file contents',
        ]);
        $directory->setCurrent($user);

        $this->getUserResponse('edit file', $user);
        $this->assertAction('edit');

        $file->delete();

        $this->getUserEditSaveResponse('Some content I want to save', $user, '', $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('File got lost along the way. Try again.');
        $this->assertNoAction();
    }

    /** @test */
    public function if_no_content_is_provided_when_trying_to_save_an_edit_then_something_went_wrong_and_that_counts_as_a_failure_in_my_books()
    {
        $user = User::factory()->create();
        $directory = Directory::factory()->create();
        $file = File::factory()->create([
            'name' => 'file',
            'owner_id' => $user->id,
            'directory_id' => $directory->id,
            'content' => 'This is the file contents',
        ]);
        $directory->setCurrent($user);

        $this->getUserResponse('edit file', $user);
        $this->assertAction('edit');
        $this->assertActionData(['edit_id' => $file->id]);

        $this->getUserEditSaveResponse('', $user, '', $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('No content to save. Aborting.');
        $this->assertNoAction();
    }

    /** @test */
    public function if_continue_flag_is_passed_then_editing_continues_because_thats_what_the_continue_flag_is_for_isnt_it()
    {
        $user = User::factory()->create();
        $directory = Directory::factory()->create();
        $file = File::factory()->create([
            'name' => 'file',
            'owner_id' => $user->id,
            'directory_id' => $directory->id,
            'content' => 'This is the file contents',
        ]);
        $directory->setCurrent($user);

        $this->getUserResponse('edit file', $user);
        $this->assertAction('edit');

        $this->getUserEditSaveResponse('Some content to save', $user, '--continue', $this->last_action_id)
            ->assertStatus(200);
        $this->assertAction('edit');

        $file->refresh();

        $this->assertEquals('Some content to save', $file->content);
    }

    /** @test */
    public function if_you_somehow_manage_not_mess_anything_up_then_your_content_is_saved()
    {
        $user = User::factory()->create();
        $directory = Directory::factory()->create();
        $file = File::factory()->create([
            'name' => 'file',
            'owner_id' => $user->id,
            'directory_id' => $directory->id,
            'content' => 'This is the file contents',
        ]);
        $directory->setCurrent($user);

        $this->getUserResponse('edit file', $user);
        $this->assertAction('edit');

        $this->getUserEditSaveResponse('Some content to save', $user, '', $this->last_action_id)
            ->assertStatus(200)
            ->assertSee('Ok.');

        $this->assertNoAction();

        $file->refresh();

        $this->assertEquals('Some content to save', $file->content);
    }
}
