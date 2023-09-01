<?php

namespace Tests\Feature\Commands;

use App\User;
use Tests\TestCase;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\DiaryEntry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;

class DiaryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_use_diary()
    {
        $this->getGuestResponse('diary')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertOk();
    }

    /** @test */
    public function diary_entries_can_be_listed()
    {
        $this->getUserResponse('diary:new Here is the content thats really long so that some of it will get cut off in the list view this bit shouldnt be visible --date="last wednesday"');
        $this->getUserResponse('diary --date="last wednesday"')
            ->assertSee('Here is the content')
            ->assertDontSee('this bit shouldnt be visible')
            ->assertOk();
    }
}
