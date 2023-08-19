<?php

namespace Mrchimp\Chimpcom;

class Str
{
    public static function splitWordsAndTags($input): array
    {
        if (is_string($input)) {
            $input = explode(' ', $input);
        }

        $tags = [];
        $words = [];

        foreach ($input as $word) {
            $word = trim($word);

            if (!empty($word)) {
                if (substr($word, 0, 1) === '@') {
                    $tags[] = substr($word, 1);
                } else {
                    $words[] = $word;
                }
            }
        }

        return [
            $words,
            $tags,
        ];
    }
}
