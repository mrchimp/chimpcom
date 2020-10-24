<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TabTestTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function tabtest_command_outputs_works_as_basic_command()
    {
        $this->getGuestResponse('tabtest')->assertStatus(200);
    }

    /** @test */
    public function tabtest_gets_empty_list_of_tabcompletions_if_no_command_is_given()
    {
        $json = $this
            ->get('/ajax/tabcomplete?cmd_in=tabtest')
            ->assertStatus(200)
            ->json();

        $this->assertCount(0, $json);
    }

    /** @test */
    public function tabtest_gets_tab_completions_for_a_words()
    {
        $json = $this
            ->get('/ajax/tabcomplete?cmd_in=tabtest a')
            ->assertStatus(200)
            ->json();

        $this->assertCount(1, $json);
        $this->assertEquals('tabtest animal', $json[0]);
    }

    /** @test */
    public function tabtest_gets_tab_completions_for_b_words()
    {
        $json = $this
            ->get('/ajax/tabcomplete?cmd_in=tabtest b')
            ->assertStatus(200)
            ->json();

        $this->assertCount(2, $json);
        $this->assertEquals('tabtest baboon', $json[0]);
        $this->assertEquals('tabtest bushbaby', $json[1]);
    }

    /** @test */
    public function tabtest_gets_tab_completions_for_c_words()
    {
        $json = $this
            ->get('/ajax/tabcomplete?cmd_in=tabtest c')
            ->assertStatus(200)
            ->json();

        $this->assertCount(3, $json);
        $this->assertEquals('tabtest cat', $json[0]);
        $this->assertEquals('tabtest crocodile', $json[1]);
        $this->assertEquals('tabtest cute dog', $json[2]);
    }
}
