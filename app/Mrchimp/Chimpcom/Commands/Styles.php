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
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(Format::title('This Is A Title<br>'));
        $output->write('Here\'s some regular text (say)<br>');
        $output->write(Format::alert('This is an alert!<br>'));
        $output->write(Format::error('Oh no! This is an error!<br>'));
        $output->write('<code>$this === some($code)</code><br>');
        $output->write(
            Format::style(
                'Auto fill (click me)',
                'autofill',
                [
                    'data-type' => 'autofill',
                    'data-autofill' => 'you clicked an autofill'
                ]
            ) . '<br>'
        );
        $output->write('<a href="#">This is a link</a><br>');
        $output->write(
            Format::listToTable(
                [
                    'Title', 'Thing 1', 'Thing 2', 'Thing 3',
                    'Title 2', 'Blah 1', 'Blah 2', 'Blah 3'
                ],
                3
            )
        );

        // @todo - Make this work
        // $this->response->cFill('This text was automatically inserted');
    }
}
