<?php
/**
 * Show some characters
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show some characters
 */
class Charmap extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('charmap');
        $this->setDescription('Show a series of unicode characters');

        $this->addOption(
            'count',
            'c',
            InputOption::VALUE_REQUIRED,
            'Number or characters to display.'
        );

        $this->addOption(
            'show_numbers',
            'n',
            null,
            'If set, the associated unicode numbers will be displayed.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getOption('count');
        if (!$count) {
            $count = 128;
        }

        $show_numbers = $input->getOption('show_numbers');

        for ($x = 33; $x < $count + 33; $x++) {
            if ($show_numbers) {
                $output->write(Format::title($x) . '&#' . $x . '; ');
            } else {
                $output->write("&#$x; ");
            }
        }
    }
}
