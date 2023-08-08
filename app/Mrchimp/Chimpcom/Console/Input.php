<?php

namespace Mrchimp\Chimpcom\Console;

use Mrchimp\Chimpcom\Str;
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

    /**
     * Take a string or an array of words and split it into an array of
     * words and an array of tags
     */
    public function splitWordsAndTags($input = []): array
    {
        return Str::splitWordsAndTags($input);
    }
}
