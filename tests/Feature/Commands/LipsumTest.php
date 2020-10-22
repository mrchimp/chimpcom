<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LipsumTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function lipsum_provides_lorem_ipsum()
    {
        $this->getGuestResponse('lipsum')
            ->assertSee('lorem ipsum')
            ->assertStatus(200);
    }

    /** @test */
    public function lipsum_can_provide_a_given_number_of_words()
    {
        $json = $this->getGuestResponse('lipsum --words=3')
            ->assertStatus(200)
            ->json();

        $output = $this->trimOutput($json['cmd_out']);

        $this->assertCount(3, explode(' ', $output));
    }

    /** @test */
    public function lipsum_can_provide_a_given_number_of_sentence()
    {
        $json = $this->getGuestResponse('lipsum --sentences=3')
            ->assertStatus(200)
            ->json();

        $output = $this->trimOutput($json['cmd_out']);

        // 4 not three, because each sentence has a full stop...
        $this->assertCount(4, explode('.', $output));
    }

    /** @test */
    public function lipsum_can_provide_a_given_number_of_paragraphs()
    {
        $json = $this->getGuestResponse('lipsum --paragraphs=3')
            ->assertStatus(200)
            ->json();

        $output = $this->trimOutput($json['cmd_out']);

        $this->assertCount(4, explode('<article>', $output));
    }

    protected function trimOutput($output)
    {
        $output = substr($output, 20);
        $output = substr($output, 0, -67);

        return $output;
    }
}
