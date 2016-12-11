<?php
/**
 * Find a memory by its name
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Find a memory by its name or description
 */
class Find extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('find');
        $this->setDescription('Find a memory by its name or description.');
        $this->addUsage('chimpcom');
        $this->addRelated('save');
        $this->addRelated('show');
        $this->addRelated('forget');
        $this->addRelated('setpublic');

        $this->addArgument(
            'search_string',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'Search term to find in memory name or description.'
        );

        $this->addOption(
            'public',
            'p',
            null,
            'Shows only public memories.'
        );

        $this->addOption(
            'private',
            'P',
            null,
            'Shows only private memories.'
        );

        $this->addOption(
            'mine',
            'm',
            null,
            'Show only your own memories.'
        );
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $show_public  = $input->getOption('public');
        $show_private = $input->getOption('private');
        $show_mine    = $input->getOption('mine');
        $search_term = '%' . implode(' ', $input->getArgument('search_string')) . '%';

        if ($show_public) {
            $item_type = 'public';
        } else if ($show_private) {
            $item_type = 'private';
        } else if ($show_mine) {
            $item_type = 'mine';
        } else {
            $item_type = 'both';
        }

        $memories = Memory::visibility($item_type)
            ->search($search_term)
            ->with('user')
            ->get();

        if (count($memories) === 0){
            $output->error('I have no recollection of that.');
            return;
        }

        $output->write(Format::memories($memories));
    }

}
