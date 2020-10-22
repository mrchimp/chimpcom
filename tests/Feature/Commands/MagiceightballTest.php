<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MagiceightballTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function magiceightball_gets_results()
    {
        $json = $this->getGuestResponse('magiceightball')
            ->assertStatus(200)
            ->json();

        $this->assertContains(
            $json['cmd_out'],
            [
                "Don't count on it.",
                "It is decidedly so.",
                "My reply is no.",
                "My sources say no.",
                "Outlook not so good.",
                "Very doubtful.",
                "Reply hazy, try again.",
                "Concentrate and ask again.",
                "Ask again later.",
                "Better not tell you now.",
                "Cannot predict now.",
                "As I see it, Yes.",
                "It is certain.",
                "Most likely.",
                "Outlook good.",
                "Signs point to yes.",
                "Without a doubt.",
                "Yes.",
                "Yes definitely.",
                "You may rely on it.",
                "Probably.",
                "Maybe.",
                "Fuck knows.",
                "Dunno.",
                "Negative.",
                "Indeed.",
                "I doubt it.",
                "Meh.",
                "Doubtful.",
                "I say no.",
                "I say yes."
            ]
        );
    }

    /** @test */
    public function magiceightball_does_star_wars_jokes_too()
    {
        $json = $this->getGuestResponse('magiceightball --force')
            ->assertStatus(200)
            ->json();

        $this->assertContains(
            $json['cmd_out'],
            [
                'It is your destiny.',
                'It is pointless to resist, my son.',
                'I\'ve got a bad feeling about this.',
                'Difficult to see. Always in motion is the future.',
                'Your feelings betray you.',
                'Search your feelings, you know it to be true.',
                'Noooooooooooooooo!'
            ]
        );
    }
}
