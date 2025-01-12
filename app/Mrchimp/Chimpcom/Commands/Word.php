<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Models\Word as WordModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Word extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('word');
        $this->setDescription('Find words');
        $this->addArgument('search_term', InputArgument::REQUIRED, 'Word to search for');
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $search_term = $input->getArgument('search_term');
        $word = WordModel::where('word', $search_term)->first();

        if (!$word) {
            $output->error('Nothing found.');
            return 1;
        }

        $output->write(e($word->word) . ' exists in the database.');
        return 0;
    }
}
