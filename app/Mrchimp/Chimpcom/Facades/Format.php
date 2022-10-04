<?php

namespace Mrchimp\Chimpcom\Facades;

use Illuminate\Support\Facades\Facade;

class Format extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Mrchimp\Chimpcom\Format::class;
    }
}
