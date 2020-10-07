<?php

namespace Mrchimp\Chimpcom\Console;

use Symfony\Component\Console\Input\StringInput;

class Input extends StringInput
{
    protected $content;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
