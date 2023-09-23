<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\DiaryEntry;
use Tests\TestCase;

class DiaryRmTest extends TestCase
{
    use DatabaseMigrations;

    protected function makeDiaryEntry()
    {
        $this->makeTestUser();

        DiaryEntry::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function can_delete_diary_entry_and_bypass_confirmation()
    {
        $this->makeDiaryEntry();
        $this->getUserResponse('diary:rm -f')->assertOk();
        $this->assertEquals(0, DiaryEntry::count());
    }

    /** @test */
    public function can_delete_diary_entry_with_confirmation()
    {
        $this->makeDiaryEntry();
        $this->getUserResponse('diary:rm')->assertOk();
        $this->getUserResponse('y', $this->user, $this->last_action_id)->assertOk();
        $this->assertEquals(0, DiaryEntry::count());
    }
}
