<?php

namespace Tests\Commands;

use Tests\TestCase;

class HiTest extends TestCase
{
    protected $response_path = 'ajax/respond/json';

    public function testResponse()
    {
        $response = $this->getResponse('hi');

        $response->assertStatus(200);
    }

    protected function getResponse($cmd)
    {
        return $this->post($this->response_path, [
            'cmd_in' => $cmd_in
        ]);
    }
}
