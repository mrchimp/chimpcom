<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mrchimp\Chimpcom\Facades\Chimpcom;
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
            'Search URL. Should be a valid URL with an optional %PARAM placeholder for search term'
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

        $name = $input->getArgument('name');
        $url = $input->getArgument('url');

        $validator = Validator::make(
            [
                'name' => $name,
                'url' => str_replace('%PARAM', 'X', $url),
            ],
            [
                'name' => [
                    Rule::notIn(Chimpcom::getCommandList()),
                ],
                'url' => [
                    'url',
                ],
            ],
            [
                'not_in' => 'Shortcut name must not match other shortcut or command names.',
            ]
        );

        if ($validator->fails()) {
            $output->writeErrors($validator);
            return 1;
        }

        if (Shortcut::where('name', $name)->count() > 0) {
            $output->error('A shortcut with that name already exists.');
            return 1;
        }

        Shortcut::create([
            'name' => $name,
            'url' => $url,
        ]);

        $output->alert('Ok.');

        return 0;
    }
}
