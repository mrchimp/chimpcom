<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Parser test
 */
class Parser extends Command
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
        $this->setHelp('Just run the command. If you don\'t give the right options it should tell you.');
        $this->addUsage('foo bar "baz bing" --option');

        $this->addArgument(
            'first_name',
            InputArgument::REQUIRED,
            'Your first name is required.'
        );

        $this->addArgument(
            'second_name',
            null,
            'Your second name is optional and defaults to "banana".',
            'banana'
        );

        $this->addArgument(
            'arguments',
            InputArgument::IS_ARRAY,
            'What are some extra words you want to add?'
        );

        $this->addOption(
            'option',
            'o',
            null,
            'This option is optional.'
        );

        $this->addOption(
            'other_option',
            'x',
            InputOption::VALUE_REQUIRED,
            'This option requires a value.'
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
        // $this->response->say('<pre>' . print_r($this->input, true) . '</pre>');
        // return;

        $output->title(Format::nl() . 'keys' . Format::nl());

        $output->write($input->getArgument('first_name') . Format::nl());
        $output->write($input->getArgument('second_name') . Format::nl());

        $output->title('Arguments' . Format::nl());

        foreach ($input->getArgument('arguments') as $argument) {
            $output->write($argument . Format::nl());
        }

        if ($input->getOption('option')) {
            $output->writeLn('Option was set!');
        }

        if ($input->getOption('other_option')) {
            $output->writeLn('Other Option was set to: ' . Format::escape($input->getOption('other_option')));
        }

        // foreach ($input as $key => $spec) {
        //     // echo '<pre>';
        //     // var_dump($spec);
        //     // echo '</pre>';
        //     $output->say($key . ' - ' . $spec->value . Format::nl());
        // }
        //
        // $output->title(Format::nl() . 'Arguments' . Format::nl());
        // foreach ($this->input->arguments as $word) {
        //     $output->say($word . Format::nl());
        // }
        // exit;

        return 0;
    }
}
