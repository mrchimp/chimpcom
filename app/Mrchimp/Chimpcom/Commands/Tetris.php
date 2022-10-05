<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Facades\Format;
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
            '&#x25A0;' . Format::nl() . '&#x25A0;' . Format::nl() . '&#x25A0;&#x25A0;',             // J
            Format::nbsp() . '&#x25A0;' . Format::nl() . Format::nbsp() . '&#x25A0;' . Format::nl() . '&#x25A0;&#x25A0;', // L
            '&#x25A0;&#x25A0;' . Format::nl() . '&#x25A0;&#x25A0;',                 // Square
            Format::nbsp() . '&#x25A0;' . Format::nl() . '&#x25A0;&#x25A0;&#x25A0;',           // T
            '&#x25A0;&#x25A0;' . Format::nl() . Format::nbsp() . '&#x25A0;&#x25A0;',           // Z
            Format::nbsp() . '&#x25A0;&#x25A0;' . Format::nl() . '&#x25A0;&#x25A0;'            // S
        ];

        $output->write('<div class="tetris">' . $tetrinos[rand(0, count($tetrinos) - 1)] . '</div>');

        return 0;
    }
}
