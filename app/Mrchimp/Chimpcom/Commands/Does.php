<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Answer questions beginning with "does"
 */
class Does extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('does');
        $this->setDescription('Answer questions beginning with "does".');
        $this->addArgument(
            'question',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'The question to answer.'
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
        $question = $input->getArgument('question');
        $question_str = implode(' ', $question);

        if (substr($question_str, -1) != '?') {
            $output->write('Questions end with question marks.');
            return 1;
        }

        $answers = [
            'I\'m not sure yet. ',
            'Sometimes. ',
            'Usually. ',
            'Sort of. ',
            'It depends how you look at it. '
        ];

        $rand = floor(rand(0, count($answers) - 1));

        $output->write($answers[$rand]);

        return 0;
    }
}
