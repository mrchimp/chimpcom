<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function parser_takes_all_the_options_and_arguments()
    {
        $this->getGuestResponse('parser first_name second_name some extra words --option --other_option=foo')
            ->assertSee('keys')
            ->assertSee('first_name')
            ->assertSee('second_name')
            ->assertSee('some')
            ->assertSee('extra')
            ->assertSee('words')
            ->assertSee('Option was set!')
            ->assertSee('Other Option was set to: foo')
            ->assertStatus(200);
    }
}
