<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Event;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class EventTest extends TestCase
{
    use DatabaseMigrations;

    protected $future_event;
    protected $past_event;
    protected $project_event;
    protected $tagged_event;
    protected $project;

    protected function createTestEvents()
    {
        $this->user = $this->makeTestUser();
        $this->project = Project::factory()->create([
            'name' => 'myproject',
            'user_id' => $this->user->id,
        ]);
        $this->future_event = Event::factory()->create([
            'date' => now()->addDay(),
            'description' => 'Flying cars and shit.',
            'user_id' => $this->user->id,
        ]);
        $this->project_event = Event::factory()->create([
            'date' => now()->addDay(),
            'description' => 'Project specific event.',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);
        $this->past_event = Event::factory()->create([
            'date' => now()->subDay(),
            'description' => 'Old timey things IDK',
            'user_id' => $this->user->id,
        ]);
        $this->tagged_event = Event::factory()->create([
            'date' => now()->addDay(),
            'description' => 'This one has tags',
            'user_id' => $this->user->id,
        ]);
        $this->tagged_event->attachTags(['foo']);
    }


    /** @test */
    public function guests_cant_use_events()
    {
        $this->getGuestResponse('event')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertOk();
    }

    /** @test */
    public function can_view_upcoming_events()
    {
        $this->createTestEvents();
        $this->getUserResponse('event')
            ->assertOk()
            ->assertSee($this->future_event->description)
            ->assertDontSee($this->past_event->description);
    }

    /** @test */
    public function can_filter_events_by_project()
    {
        $this->createTestEvents();
        $this->getUserResponse('event --project=myproject')
            ->assertOk()
            ->assertSee($this->project_event->description)
            ->assertDontSee($this->past_event->description)
            ->assertDontSee($this->tagged_event->description);
    }

    /** @test */
    public function can_filter_events_by_tag()
    {
        $this->createTestEvents();
        $this->getUserResponse('event @foo')
            ->assertOk()
            ->assertSee($this->tagged_event->description)
            ->assertDontSee($this->project_event->description)
            ->assertDontSee($this->past_event->description);
    }
}
