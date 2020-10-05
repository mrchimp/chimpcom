<?php

namespace Mrchimp\Chimpcom;

use Parsedown as BaseParsedown;

class Parsedown extends BaseParsedown
{
    protected function inlineImage($Excerpt)
    {
        return;
    }

    protected function blockHeader($Line)
    {
        if (isset($Line['text'][1])) {
            $level = 1;

            while (isset($Line['text'][$level]) and $Line['text'][$level] === '#') {
                $level ++;
            }

            if ($level > 6) {
                return;
            }

            $text = trim($Line['text'], '# ');

            $Block = array(
                'element' => array(
                    'name' => 'div',
                    'text' => str_repeat('#', $level) . ' ' . $text,
                    'handler' => 'line',
                    'attributes' => [
                        'class' => 'blue_highlight'
                    ],
                ),
            );

            return $Block;
        }
    }

    protected function paragraph($Line)
    {
        $Block = array(
            'element' => array(
                'name' => 'p',
                'text' => $Line['text'],
                'handler' => 'line',
            ),
        );

        return $Block;
    }
}
