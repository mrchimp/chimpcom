<?php

namespace Mrchimp\Chimpcom;

use Illuminate\Support\ServiceProvider;
use Mrchimp\Chimpcom\Chimpcom;

class ChimpcomServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('chimpcom', Chimpcom::class);

        if (request()->input('format') === 'cli') {
            $this->app->bind(
                \Mrchimp\Chimpcom\Format::class,
                \Mrchimp\Chimpcom\FormatCli::class
            );
        } else {
            $this->app->bind(
                \Mrchimp\Chimpcom\Format::class,
                \Mrchimp\Chimpcom\FormatHtml::class
            );
        }
    }
}
