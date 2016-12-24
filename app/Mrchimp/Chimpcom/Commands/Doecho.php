<?php
/**
 * Echo echo echo
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Echo echo echo
 */
class Doecho extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('echo');
        $this->setDescription('Echo echo echo');
        $this->addArgument(
            'echo',
            InputArgument::OPTIONAL,
            'Echo echo echo echo.'
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
        $word = $input->getArgument('echo');

        if (!$word) { $word = 'echo'; }

        $output->write("$word <span style=\"font-size: 75%\">$word</span> <span style=\"font-size: 50%\">$word</span> <span style=\"font-size: 25%\">$word</span>");
    }
}
