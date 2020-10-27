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

        if (!$user->is_admin) {
            $output->error(__('chimpcom.not_admin'));
            return 1;
        }

        $alias_name   = $input->getArgument('alias');
        $command_name = $input->getArgument('command');

        if (!$alias_name) {
            $aliases = ChimpcomAlias::all();
            $out = [];

            foreach ($aliases as $alias) {
                $out[] = $alias->name;
                $out[] = ' ➞ ';
                $out[] = $alias->alias;
            }

            $output->write(Format::listToTable($out, 3, false));
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

        // @todo Fix these column names
        $alias = new ChimpcomAlias();
        $alias->name  = $alias_name;
        $alias->alias = $command_name;

        if ($alias->save()) {
            $output->alert('Ok.');
        } else {
            $output->error('There was a problem. Try again.');
        }

        return 0;
    }
}
