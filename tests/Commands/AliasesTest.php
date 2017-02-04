<?php

namespace Tests\Commands;

class AliasesTest extends CommandTestTemplate
{
    public function testGuestResponse()
    {
        $this->getGuestResponse('aliases')
            ->assertStatus(200);
    }
}
