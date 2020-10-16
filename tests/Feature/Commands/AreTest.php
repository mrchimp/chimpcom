<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AreTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function questions_beginning_with_are_require_question_marks()
    {
        $this->getGuestResponse('are this work')
            ->assertSee('Questions end with question marks')
            ->assertStatus(200);
    }

    /** @test */
    public function are_you_sentient_is_a_question_that_can_be_asked()
    {
        $this->getGuestResponse('are you sentient?')
            ->assertSee('Pretty much')
            ->assertStatus(200);
    }

    /** @test */
    public function is_chimpcom_human()
    {
        $this->getGuestResponse('are you human?')
            ->assertSee('What does it look like?')
            ->assertStatus(200);
    }

    /** @test */
    public function are_gives_random_answers()
    {
        $json = $this->getGuestResponse('are things happening?')
            ->assertStatus(200)
            ->json();

        $this->assertContains($json['cmd_out'], [
            'I\'m not sure yet. ',
            'No way. ',
            'Definitely. ',
            'It depends on your point of view. '
        ]);
    }
}
