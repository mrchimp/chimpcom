<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DiaryGraphTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cant_use_diary_graph()
    {
        $this->getGuestResponse('diary:graph')
            ->assertSee(__('chimpcom.must_log_in'))
            ->assertOk();
    }

    /** @test */
    public function user_can_get_redirected_to_graph()
    {
        $json = $this->getUserResponse('diary:graph --meta foo')
            ->assertOk()
            ->json();

        $this->assertEquals(route('graphs.diary') . '?meta[]=foo', $json['openWindow']);
    }
}
