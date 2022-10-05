<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Encode a string in base64
 */
class Base64encode extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('base64encode');
        $this->setDescription('Encodes a base64 encoded string.');
        $this->addArgument(
            'input',
            InputArgument::REQUIRED,
            'A plaintext string to be encoded.'
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
        $decoded = base64_encode(utf8_encode($encoded));

        $output->write(Format::escape($decoded));

        return 0;
    }
}
