<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StylesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function styles_command_gets_text_in_all_styles_for_testing_purposes()
    {
        $json = $this
            ->getGuestResponse('styles')
            ->assertSee('<span class=\"blue_highlight\">This Is A Title<\/span><br>', false)
            ->assertSee(e('Here\'s some regular text (say)') . '<br>', false)
            ->assertSee('<span class=\"green_highlight\">This is an alert!<\/span><br>', false)
            ->assertSee('<span class=\"red_highlight\">Oh no! This is an error!<\/span><br>', false)
            ->assertSee('<code>$this === some($code)<\/code><br>', false)
            ->assertSee('<span class=\"autofill\" data-type=\"autofill\" data-autofill=\"you clicked an autofill\">Auto fill (click me)<\/span><br>', false)
            ->assertSee('<a href=\"https:\/\/example.com\"  data-foo=\"bar\">This is a link<\/a><br>', false)
            ->assertSee('<table><tr><td>Title<\/td><td>Thing 1<\/td><td>Thing 2<\/td><\/tr><tr><td>Thing 3<\/td><td>Title 2<\/td><td>Blah 1<\/td><\/tr><tr><td>Blah 2<\/td><td>Blah 3<\/td><\/tr><\/table>', false)
            ->assertStatus(200)
            ->json();

        $this->assertEquals('This text was automatically inserted', $json['cmd_fill']);
    }
}
