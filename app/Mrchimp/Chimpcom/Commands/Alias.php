<?php
/**
 * Add a command alias
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Validator;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must be logged in to use this command.');
            return false;
        }

        $user = Auth::user();

        if (!$user->is_admin) {
            $output->error('No.');
            return false;
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

            $output->write(Format::listToTable($out, 3, true));
            return;
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
            return false;
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
    }
}
