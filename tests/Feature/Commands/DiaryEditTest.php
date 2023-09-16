<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\DiaryEntry;

class DiaryEditTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_use_diary()
    {
        $this->getGuestResponse('diary:edit content')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertOk();
    }

    /** @test */
    public function diary_entries_can_be_edited()
    {
        $this->getUserResponse('diary:new "Here is a new diary entry."')
            ->assertOk();
        $this->getUserResponse('diary:edit')
            ->assertOk();
        $this->getUserEditSaveResponse('This entry has been updated.', $this->user, '', $this->last_action_id)
            ->assertOk();

        $this->assertEquals(1, DiaryEntry::count());
        $entry = DiaryEntry::first();
        $this->assertEquals('This entry has been updated.', $entry->content);
    }

    /** @test */
    public function diary_entries_on_other_days_can_be_edited()
    {
        $this->getUserResponse('diary:new --date="Last wednesday" "Here is a new diary entry."')
            ->assertOk();
        $this->getUserResponse('diary:edit --date="Last wednesday"')
            ->assertOk();
        $this->getUserEditSaveResponse('This entry has been updated.', $this->user, '', $this->last_action_id)
            ->assertOk();

        $this->assertEquals(1, DiaryEntry::count());
        $entry = DiaryEntry::first();
        $this->assertEquals('This entry has been updated.', $entry->content);
    }
}
