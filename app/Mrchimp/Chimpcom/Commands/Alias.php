<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Validator;

/**
 * Add a command alias
 */
class Alias extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('alias');
        $this->setDescription('Create a new command alias. If no arguments are given, list existing aliases.');
        $this->addArgument(
            'alias',
            InputArgument::OPTIONAL,
            'The command you want to type.'
        );
        $this->addArgument(
            'command',
            InputArgument::OPTIONAL,
            'The command that ALIAS will be translated to.'
        );
        $this->addOption(
            'global',
            'g',
            null,
            'Allow this alias to work for anyone'
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
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            return 1;
        }

        $user = Auth::user();

        $user_id = $user->id;

        if ($user->is_admin && $input->getOption('global')) {
            $user_id = null;
            return 1;
        }

        $alias_name   = $input->getArgument('alias');
        $command_name = $input->getArgument('command');

        if (!$alias_name) {
            $aliases = ChimpcomAlias::all();
            $out = [];

            foreach ($aliases as $alias) {
                $out[] = e($alias->name);
                $out[] = ' ➞ ';
                $out[] = e($alias->alias);
            }

            $output->write(Format::listToTable(
                $out,
                3,
                false,
                [
                    'Name',
                    '',
                    'Alias'
                ]
            ));
            return 2;
        }

        $validator = Validator::make([
            'name' => $alias_name,
            'alias' => $command_name,
        ], [
            'name'  => 'required|unique:aliases,name',
            'alias' => 'required',
        ]);

        if ($validator->fails()) {
            $output->writeErrors($validator);
            return 3;
        }

        $alias = ChimpcomAlias::create([
            'name' => $alias_name,
            'alias' => $command_name,
            'user_id' => $user_id,
        ]);

        if ($alias->save()) {
            $output->alert('Ok.');
        } else {
            $output->error('There was a problem. Try again.');
        }

        return 0;
    }
}
