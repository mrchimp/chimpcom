<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryGraph extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary:graph');
        $this->setDescription('Display a graph of diary meta data.');
        $this->addUsage('diary:graph --meta=foo');
        $this->addRelated('diary');
        $this->addRelated('diary:new');
        $this->addRelated('diary:edit');
        $this->addRelated('diary:read');
        $this->addRelated('project');
        $this->addRelated('tag');
        $this->addOption(
            'meta',
            'm',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Add meta data to the entry. E.g. --meta=foo:bar'
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        return $this->showGraph($input, $output);
    }

    protected function showGraph(InputInterface $input, OutputInterface $output): int
    {
        $meta = $input->getOption('meta');

        if (empty($meta)) {
            $output->error('You must provide at least one meta field to graph.');
            return 1;
        }

        $query = implode('&', array_map(fn ($item) => 'meta[]=' . $item, $meta));

        $url = route('graphs.diary') . '?' . $query;
        $output->openWindow($url);
        $output->write(Format::alert('Opening graph in new tab.'));
        return 0;
    }
}
