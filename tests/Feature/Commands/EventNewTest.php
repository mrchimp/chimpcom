<?php

namespace Tests\Feature\Commands;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Event;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class EventNewTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function events_are_not_for_guests()
    {
        $this->getGuestResponse('event:new "Next tuesday" This will fail.')
            ->assertOk()
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function user_can_create_event()
    {
        $this->getUserResponse('event:new --date "Next tuesday" Something is happening.')
            ->assertOk()
            ->assertSee('Event created');

        $this->assertEquals(1, Event::count());

        $event = Event::first();
        $this->assertEquals('Something is happening.', $event->description);

        $this->markTestIncomplete('Need to check date.');
    }

    /** @test */
    public function event_can_be_associated_with_a_project()
    {
        $this->user = User::factory()->create();
        Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);
        $this->getUserResponse('event:new --project=myproject --date "Next tuesday" Something is happening')
            ->assertOk();

        $event = Event::first();
        $this->assertEquals('myproject', $event->project->name);
    }

    /** @test */
    public function event_can_have_tags()
    {
        $this->getUserResponse('event:new --date "Next tuesday" Something happens. @exciting')->assertOk();

        $event = Event::first();

        $this->assertEquals('Something happens.', $event->description);
        $this->assertCount(1, $event->tags);
        $this->assertEquals('exciting', $event->tags->first()->tag);
    }
}
