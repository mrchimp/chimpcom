<?php

namespace Mrchimp\Chimpcom;

use Illuminate\Support\ServiceProvider;
use Mrchimp\Chimpcom\Chimpcom;

class ChimpcomServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('chimpcom', Chimpcom::class);
    }
}
