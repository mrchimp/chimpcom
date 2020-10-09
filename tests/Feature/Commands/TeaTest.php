<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class TeaTest extends TestCase
{
    /** @test */
    public function tea_returns_response_from_inputs()
    {
        $response = $this->getGuestResponse('tea tom dick harry')
            ->assertStatus(200);

        $answer = $response->json()['cmd_out'];

        $this->assertContains($answer, [
            'Tom is on hot beverage duty.',
            'Dick is on hot beverage duty.',
            'Harry is on hot beverage duty.',
        ]);
    }

    /** @test */
    public function if_chosen_person_is_you_then_grammar_is_updated_accordingly()
    {
        $response = $this->getGuestResponse('tea you you you')
            ->assertStatus(200);

        $answer = $response->json()['cmd_out'];

        $this->assertEquals('You are on hot beverage duty.', $answer);
    }
}
