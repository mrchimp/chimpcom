<?php

namespace Tests\Commands;

use Tests\TestCase;

class CommandTestTemplate extends TestCase
{
    protected $response_path = 'ajax/respond/json';

    protected function getResponse($cmd_in)
    {
        return $this->post($this->response_path, [
            'cmd_in' => $cmd_in
        ]);
    }
}
