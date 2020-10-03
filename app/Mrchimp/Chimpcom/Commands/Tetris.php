<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show a random tetromino
 */
class Tetris extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('tetris');
        $this->setDescription('Show a random tetromino.');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tetrinos = [
            '&#x25A0;&#x25A0;&#x25A0;&#x25A0;',                     // Line
            '&#x25A0;<br>&#x25A0;<br>&#x25A0;&#x25A0;',             // J
            '&nbsp;&#x25A0;<br>&nbsp;&#x25A0;<br>&#x25A0;&#x25A0;', // L
            '&#x25A0;&#x25A0;<br>&#x25A0;&#x25A0;',                 // Square
            '&nbsp;&#x25A0;<br>&#x25A0;&#x25A0;&#x25A0;',           // T
            '&#x25A0;&#x25A0;<br>&nbsp;&#x25A0;&#x25A0;',           // Z
            '&nbsp;&#x25A0;&#x25A0;<br>&#x25A0;&#x25A0;'            // S
        ];

        $output->write('<div class="tetris">' . $tetrinos[rand(0, count($tetrinos) - 1)] . '</div>');

        return 0;
    }
}
