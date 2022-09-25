<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Alias;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * View command aliases
 */
class Aliases extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('aliases');
        $this->setDescription('Show existing aliases.');
        $this->addOption(
            'global',
            null,
            null,
            'Show global aliases, without this, only your personal aliases will be shown.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            $output->setStatusCode(404);

            return 1;
        }

        $aliases = Alias::query()
            ->when($input->getOption('global'), function ($query) {
                $query->whereNull('user_id');
            }, function ($query) {
                $query->whereNotNull('user_id')
                    ->where('user_id', Auth::id());
            })
            ->get()
            ->map(function ($alias) {
                return $alias->only(['name', 'alias']);
            })
            ->flatten()
            ->toArray();
        $output->write(Format::listToTable($aliases, 2, false, ['alias', 'command']));

        return 0;
    }
}
