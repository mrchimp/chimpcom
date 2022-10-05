<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Decode a string in base64
 */
class Base64decode extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('base64decode');
        $this->setDescription('Decodes a base64 encoded string.');
        $this->setHelp('If the resulting output is binary it will not be displayed.');
        $this->addUsage('base64decode SGVsbG8sIHdvcmxkIQ==');
        $this->addArgument(
            'input',
            InputArgument::REQUIRED,
            'A base 64 encoded string.'
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
        $encoded = $input->getArgument('input');
        $decoded = base64_decode(utf8_encode($encoded));

        $output->write(Format::escape($decoded));

        return 0;
    }
}
