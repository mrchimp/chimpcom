<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mrchimp\Chimpcom\Format;

class Styles extends Command
{

    protected function configure()
    {
        $this->setName('styles');
        $this->setDescription('Previews the formatter styles');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(Format::title('This Is A Title<br>'));
        $output->write('Here\'s some regular text (say)<br>');
        $output->write(Format::alert('This is an alert!<br>'));
        $output->write(Format::error('Oh no! This is an error!<br>'));
        $output->write('<code>$this === some($code)</code><br>');
        $output->write(Format::style('Auto fill (click me)', '', [
            'data-type' => 'autofill',
            'data-autofill' => 'you clicked an autofill'
        ]));

        // @todo - Make this work
        // $this->response->cFill('This text was automatically inserted');
    }
}
