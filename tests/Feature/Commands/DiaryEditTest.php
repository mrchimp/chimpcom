<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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
        $this->markTestIncomplete('Editing diary entries is not tested!');
    }
}
