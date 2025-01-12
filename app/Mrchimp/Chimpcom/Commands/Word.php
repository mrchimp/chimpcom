<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Facades\Format;
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
        $this->addArgument(
            'search_term',
            InputArgument::REQUIRED,
            'Word to search for. By default, a whole-word search.'
        );
        $this->addOption('contains', 'c', null, 'Show words that contain the search term');
        $this->addOption('beginning', 'b', null, 'Show words that begin with the search term');
        $this->addOption('ending', 'e', null, 'Show words that end with the search term');
        $this->addOption('total', 't', null, 'Only show the number of results');
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $search_term = $input->getArgument('search_term');
        $contains = $input->getOption('contains');
        $beginning = $input->getOption('beginning');
        $ending = $input->getOption('ending');
        $total = $input->getOption('total');

        if ($contains) {
            $search_term = '%' . $search_term . '%';
        } elseif ($beginning) {
            $search_term = $search_term . '%';
        } elseif ($ending) {
            $search_term = '%' . $search_term;
        }

        if ($contains || $beginning || $ending) {
            $words = WordModel::where('word', 'like', $search_term)->get();
        } else {
            $words = WordModel::where('word', $search_term)->get();
        }

        if ($words->isEmpty()) {
            $output->error('Nothing found.');
            return 1;
        }

        $output->write("Found {$words->count()} results");

        if ($total) {
            return 0;
        }

        $words->each(function ($word) use ($output) {
            $output->write(e($word->word) . Format::nl());
        });

        return 0;
    }
}
