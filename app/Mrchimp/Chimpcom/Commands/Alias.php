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
    protected function configure()
    {
        $this->setName('alias');
        $this->setDescription('Create a new command alias. If no arguments are given, list existing aliases.');
        $this->addArgument(
            'alias',
            InputArgument::OPTIONAL,
            'The alias for the command.'
        );
        $this->setName('register2');
        $this->setDescription('Register step 3.');
        $this->addArgument(
            'command',
            InputArgument::OPTIONAL,
            'The command to alias.'
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $alias_name = $input->getArgument('alias');
        $command_name = $input->getArgument('command');

        if (!$alias_name) {
            $aliases = ChimpcomAlias::get();
            $output = [];
            foreach ($aliases as $alias) {
                $output[] = $alias->name;
                $output[] = $alias->alias;
            }
            $this->response->say(Format::listToTable($output, 2, true));
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
