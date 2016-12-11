<?php
/**
 * Change directory
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Change directory
 */
class Cd extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('cd');
        $this->setDescription('Change the shell working directory.');
        $this->setHelp('Change the current directory to DIR. The default DIR is the value of the HOME shell variable.');

        $this->addArgument(
            'dir',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The directory to change to.'
        );
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        $dir_str = implode(' ', $dir);

        if (!$dir) {
            $output->write('You remain here.');
        } else if ($dir_str == 'penguin'){
            $output->write('You are inside a penguin. It is dark.');
        } else if ($dir_str == 'c:' || $dir_str == 'C:') {
            $output->write('What d\'you think this is, Windows?');
        } else if ($dir_str == '..'){
            $output->write('You claw at the directory above you but cannot get a purchase.');
        } else {
            $output->error('No such file or directory.');
        }
    }

}
