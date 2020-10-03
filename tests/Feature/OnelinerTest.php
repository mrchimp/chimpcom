<?php

namespace Tests\Feature;

use Mrchimp\Chimpcom\Models\Oneliner;
use Tests\TestCase;

class OnelinerTest extends TestCase
{
    /** @test */
    public function oneliner_returns_a_response_if_it_exists()
    {
        Oneliner::create([
            'command' => 'testoneliner',
            'response' => 'This is the response',
        ]);

        $this->getGuestResponse('testoneliner')
            ->assertSee('This is the response');
    }
}
