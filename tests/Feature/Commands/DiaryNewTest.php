<?php

namespace Tests\Feature\Commands;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Arr;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\DiaryEntry;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DiaryNewTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_use_diary()
    {
        $this->getGuestResponse('diary:new content')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertOk();
    }

    /** @test */
    public function users_can_save_diary_entries()
    {
        $this->getUserResponse('diary:new Here is the content')
            ->assertSee('Diary entry saved.')
            ->assertOk();

        $entry = DiaryEntry::first();

        $this->assertEquals('Here is the content', $entry->content);
    }

    /** @test */
    public function diary_entries_can_be_tagged()
    {
        $this->getUserResponse('diary:new Here is @some content @hashtag')
            ->assertOk();

        $entry = DiaryEntry::first();

        $this->assertCount(2, $entry->tags);
        $this->assertEquals('some', $entry->tags[0]->tag);
        $this->assertEquals('hashtag', $entry->tags[1]->tag);
        $this->assertEquals('Here is content', $entry->content);
        $this->getUserResponse('diary:read')
            ->assertOk()
            ->assertSee('@some')
            ->assertSee('@hashtag');
    }

    /** @test */
    public function diary_entries_can_be_attached_to_a_project()
    {
        $this->user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);

        $this->getUserResponse('diary:new Here is the content --project=myproject')
            ->assertOk();

        $entry = DiaryEntry::first();

        $this->assertEquals($project->id, $entry->project_id);
        $this->assertEquals('Here is the content', $entry->content);
    }

    /** @test */
    public function diary_entries_can_be_created_for_a_specific_date_time()
    {
        $this->getUserResponse('diary:new Here is the content --date="2020-01-01 12:00"')
            ->assertOk();

        $entry = DiaryEntry::first();

        $this->assertEquals('2020-01-01 12:00:00', $entry->date->format('Y-m-d H:i:s'));
        $this->assertEquals('Here is the content', $entry->content);
    }

    /** @test */
    public function diary_entries_can_have_metadata()
    {
        $this->getUserResponse('diary:new Here is the content --meta=foo:bar --meta floopy:bloopy')
            ->assertSee('Diary entry saved.')
            ->assertOk();

        $entry = DiaryEntry::first();

        $this->assertEquals('bar', Arr::get($entry->meta, 'foo'));
        $this->assertEquals('bloopy', Arr::get($entry->meta, 'floopy'));
    }

    /** @test */
    public function if_a_diary_entry_exists_the_new_content_will_be_appended_and_made_editable()
    {
        $this->getUserResponse('diary:new Here is a new entry.')->assertOk();
        $json = $this->getUserResponse('diary:new Here is some more content.')
            ->assertOk()
            ->json();

        $this->getUserEditSaveResponse('I gave up and replaced both with this.', $this->user, '', $this->last_action_id)
            ->assertOk();
    }
}
