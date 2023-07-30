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

    /**
     * Take a string or an array of words and split it into an array of
     * words and an array of tags
     */
    public function splitWordsAndTags($input = []): array
    {
        if (is_string($input)) {
            $input = explode(' ', $input);
        }

        $tags = [];
        $words = [];

        foreach ($input as $word) {
            if (substr($word, 0, 1) === '@') {
                $tags[] = substr($word, 1);
            } else {
                $words[] = $word;
            }
        }

        return [
            $words,
            $tags,
        ];
    }
}
