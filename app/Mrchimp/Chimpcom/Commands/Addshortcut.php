<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Validator;
use Chimpcom;
use Mrchimp\Chimpcom\Models\Shortcut;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Addshortcut extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('addshortcut');
        $this->setDescription('Add a shortcut command.');
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Search command name.'
        );
        $this->addArgument(
            'url',
            InputArgument::REQUIRED,
            'Search URL. Should be a valid URL with %PARAM placeholder for search term'
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

        $name = $input->getArgument('name');
        $url = $input->getArgument('url');

        $available_commands = Chimpcom::getCommandList();

        $validator = Validator::make([
            'name' => $name,
            'url' => $url,
        ],[
            'name' => 'required|not_in:'.implode(',', $available_commands),
            'url' => 'required|url',
        ],[
            'not_in' => 'Shortcut name must not match other shortcut or command names.',
        ]);

        if ($validator->fails()) {
            $output->writeErrors($validator);
            return;
        }

        $shortcut = new Shortcut();
        $shortcut->name = $name;
        $shortcut->url = $url;

        if ($shortcut->save()) {
            $output->alert('Ok.');
        } else {
            $output->error('There was an error. Try again.');
        }
    }
}
