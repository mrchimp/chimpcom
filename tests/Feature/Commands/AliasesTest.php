<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class AliasesTest extends TestCase
{
    public function testGuestResponse()
    {
        $this->getGuestResponse('aliases')
            ->assertStatus(200);
    }
}
