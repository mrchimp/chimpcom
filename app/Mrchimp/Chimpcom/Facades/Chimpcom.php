<?php

namespace Mrchimp\Chimpcom\Facades;

use Illuminate\Support\Facades\Facade;

class Chimpcom extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'chimpcom';
    }
}
