<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\DiaryEntry;

class DiaryReadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_use_diary_read()
    {
        $this->getGuestResponse('diary:read')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertOk();
    }

    /** @test */
    public function diary_entries_can_be_read()
    {
        $this->getUserResponse('diary:new Here is the content --date "last wednesday"');
        $this->getUserResponse('diary:read --date "last wednesday"')
            ->assertSee('Here is the content')
            ->assertOk();

        $this->getUserResponse('diary:read --date today')
            ->assertSee('No entry found for that date');
    }

    /** @test */
    public function users_cant_read_each_others_diary_entries()
    {
        DiaryEntry::factory()->create([
            'content' => 'You shouldnt see this.',
            'user_id' => 999
        ]);

        $this->getUserResponse('diary:read')
            ->assertOk()
            ->assertDontSee('You shouldnt see this.');
    }

    /** @test */
    public function can_view_diary_entry_metadata()
    {
        $this->getUserResponse('diary:new Here is the content --meta foo:bar --meta floopy:bloopy');
        $this->getUserResponse('diary:read')
            ->assertOk()
            ->assertSee('foo: bar')
            ->assertSee('floopy: bloopy');
    }
}
