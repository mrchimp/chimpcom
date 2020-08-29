<?php

/**
 * Get some answers
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get some answers
 */
class Are extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('are');
        $this->setDescription('Answer some questions.');

        $this->addArgument(
            'question',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'The question you need answering.'
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
        $words = $input->getArgument('question');
        $question = implode(' ', $words);

        if (substr($question, -1) != '?') {
            $output->write('Questions end with question marks.');
            return 1;
        }

        if ($this->setAndEqual($words, 0, 'you')) {
            if ($this->setAndEqual($words, 1, 'sentient?')) {
                $output->write('Pretty much.');
            } else if ($this->setAndEqual($words, 1, 'human?')) {
                $output->write('What does it look like?');
            }

            return 1;
        }

        $answers = [
            'I\'m not sure yet. ',
            'No way. ',
            'Definitely. ',
            'It depends on your point of view. '
        ];

        $rand = floor(rand(0, count($answers) - 1));

        $output->write($answers[$rand]);

        return 0;
    }

    /**
     * Returns true if the $index of $array exists and is equal to $value
     *
     * @param  array    $array
     * @param  integer  $index
     * @param  various  $value
     * @return boolean
     */
    protected function setAndEqual($array, $index, $value)
    {
        if (!isset($array[$index])) {
            return false;
        }

        return $array[$index] === $value;
    }
}
