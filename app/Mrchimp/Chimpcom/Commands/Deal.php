<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Cards\Deck;
use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Deals a card from a pack
 */
class Deal extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('deal');
        $this->setDescription('Deals some random cards from a deck.');

        $this->addOption(
            'count',
            'c',
            InputOption::VALUE_REQUIRED,
            'Number of cards to deal. 6 by default.'
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
        $count = $input->getOption('count');

        if (!$count) {
            $count = 6;
        }

        $deck = new Deck();
        $hand = $deck->deal($count);

        // Output ten per line
        $x = 0;

        foreach ($hand as $card) {
            if ($x % 10 == 0 && $x > 0) {
                $output->write('<br>');
            }

            $output->write(Format::style($card->getSuit() . $card->getRank(), 'card ' . $card->getColor()));
            $x++;
        }

        return 0;
    }
}
