<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\Project;
use Tests\TestCase;

class CommandTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_a_commands_tab_completions()
    {
        $this->user = factory(User::class)->create();

        factory(Project::class)->create([
            'user_id' => $this->user->id,
            'name' => 'chimpcom',
        ]);

        $json = $this->actingAs($this->user)
            ->get('/ajax/tabcomplete?cmd_in=project+set+c')
            ->assertStatus(200)
            ->json();

        $this->assertCount(1, $json);
        $this->assertEquals('project set chimpcom', $json[0]);
    }
}
