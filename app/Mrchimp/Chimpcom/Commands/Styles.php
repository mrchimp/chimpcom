<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Styles extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('styles');
        $this->setDescription('Previews the formatter styles');
    }

    /**
     * Run the command
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(Format::title('This Is A Title'), true);
        $output->write(e('Here\'s some regular text (say)'), true);
        $output->write(Format::alert('This is an alert!'), true);
        $output->write(Format::error('Oh no! This is an error!'), true);
        $output->write('<code>$this === some($code)</code>', true);
        $output->write(
            Format::style(
                'Auto fill (click me)',
                'autofill',
                [
                    'data-type' => 'autofill',
                    'data-autofill' => 'you clicked an autofill'
                ]
            ),
            true
        );
        $output->write(Format::link('This is a link', 'https://example.com', [
            'data-foo' => 'bar',
        ]), true);
        $output->write(
            Format::listToTable(
                [
                    'Title 1', 'Title 2', 'Title 3',
                    'Thing 1', 'Thing 2', 'Thing 3',
                    'Blah 1', 'Blah 2', 'Blah 3'
                ],
                3
            ),
            true
        );
        $output->write(
            Format::listToTable(
                [
                    'Thing 1', 'Thing 2', 'Thing 3',
                    'Blah 1', 'Blah 2', 'Blah 3',
                ],
                3,
                false,
                [
                    'Title 1',
                    'Title 2',
                    'Title 3',
                ]
            )
        );

        $output->cFill('This text was automatically inserted');

        return 0;
    }
}
