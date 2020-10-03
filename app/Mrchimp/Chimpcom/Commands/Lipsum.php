<?php

namespace Mrchimp\Chimpcom\Commands;

use joshtronic\LoremIpsum;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show some lorem ipsum
 */
class Lipsum extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('lipsum');
        $this->setDescription('Show some lorem ipsum.');
        $this->addOption(
            'words',
            'w',
            InputOption::VALUE_REQUIRED,
            'Number of words',
            null
        );
        $this->addOption(
            'sentences',
            's',
            InputOption::VALUE_REQUIRED,
            'Number of sentences',
            null
        );
        $this->addOption(
            'paragraphs',
            'p',
            InputOption::VALUE_REQUIRED,
            'Number of paragraphs to show',
            null
        );
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
        $lipsum = new LoremIpsum();

        $output->write('<div class="lipsum">');

        if ($input->getOption('words')) {
            $output->write($lipsum->words($input->getOption('words')));
        } elseif ($input->getOption('sentences')) {
            $output->write($lipsum->sentences($input->getOption('sentences'), 'p'));
        } elseif ($input->getOption('paragraphs')) {
            $output->write($lipsum->paragraphs($input->getOption('paragraphs'), ['article', 'p']));
        } else {
            $output->write($lipsum->words(15));
        }

        $output->write('</div>');
        $output->write('<a href="http://www.lipsum.com/" target="_blank">Get more</a>');

        return 0;
    }
}
