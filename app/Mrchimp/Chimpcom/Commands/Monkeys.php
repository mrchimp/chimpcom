<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A finite amount of artificial monkeys
 */
class Monkeys extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('parser');
        $this->setDescription('Test command for the new parser.');
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->title('Chimpcom Infinite Monkey Shakespeare Project<br>');

        // Define an alphabet to choose letters from.
        // I think I added extra spaces so that it spaces the
        // words the words out semi-realistically
        $alphabet = 'abcdefghijklmnopqrstuvwxyz    ';

        // Count the letters of the alphabet so that we don't
        // have to do it in a loop below.
        $size     = strlen($alphabet);
        $target   = 'to be or not to be';
        $apechat  = '';

        for ($x = 1; $x < 1000; $x++) {
            $rand = mt_rand(0, $size - 1);
            $apechat  = $apechat  . $alphabet[$rand];
        }

        // First, check if we've got the whole thing.
        if (strpos($apechat, $target) !== false) {
            $apechat = str_replace($target, $this->alert($target, [], true), $apechat);
            $output->alert('<br>SUCCESS!<br><br>');
            $output->write($apechat);

            return 0;
        }

        $search  = [
            'to',
            'be',
            'or',
            'not'
        ];

        $replace = [
            '<span class="blue_highlight">to</span>',
            '<span class="blue_highlight">be</span>',
            '<span class="blue_highlight">or</span>',
            '<span class="blue_highlight">not</span>'
        ];

        $apechat  = str_replace($search, $replace, $apechat);

        $output->write($apechat);

        return 0;
    }
}
