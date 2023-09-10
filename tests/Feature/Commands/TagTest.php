<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\DiaryEntry;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Tag;
use Mrchimp\Chimpcom\Models\Task;
use Tests\TestCase;

class TagTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function tags_arent_for_guests()
    {
        $this->getGuestResponse('tag')
            ->assertStatus(404)
            ->assertSee(__('chimpcom.must_log_in'));
    }

    /** @test */
    public function tag_command_lists_tags()
    {
        Tag::factory()->create([
            'tag' => 'green',
        ]);
        Tag::factory()->create([
            'tag' => 'blue',
        ]);

        $this->getUserResponse('tag')
            ->assertSee('green')
            ->assertSee('blue')
            ->assertStatus(200);
    }

    /** @test */
    public function tag_command_can_be_filtered_by_project()
    {
        $project = $this->createProject();

        $task = Task::factory()->create([
            'project_id' => $project->id,
        ]);
        $task->attachTags(['relevantTaskTag']);

        $diary_entry = DiaryEntry::factory()->create([
            'project_id' => $project->id,
        ]);
        $diary_entry->attachTags(['relevantDiaryTag']);

        $note = Memory::factory()->create([
            'project_id' => $project->id,
        ]);
        $note->attachTags(['relevantNoteTag']);

        Tag::factory()->create([
            'tag' => 'irrelevantTaskTag',
        ]);

        Tag::factory()->create([
            'tag' => 'irrelevantDiaryEntryTag',
        ]);

        Tag::factory()->create([
            'tag' => 'irrelevantNoteTag',
        ]);

        $this->getUserResponse('tag --project="' . $project->name . '"')
            ->assertSee('relevantTaskTag')
            ->assertDontSee('irrelevantTaskTag')
            ->assertSee('relevantDiaryTag')
            ->assertDontSee('irrelevantDiaryEntryTag')
            ->assertSee('relevantNoteTag')
            ->assertDontSee('irrelevantNoteTag')
            ->assertStatus(200);
    }
}
