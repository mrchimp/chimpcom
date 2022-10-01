<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Shortcut as ShortcutModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Shortcut extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('shortcut');
        $this->setDescription('Add a shortcut command.');
        $this->addUsage('shortcut --global google https://www.google.com/search?q=%PARAM');
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
        $this->addOption(
            'global',
            'g',
            null,
            'Allows the shortcut to be used by anybody. Admins only.'
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
        $name = $input->getArgument('name');
        $url = $input->getArgument('url');
        $global = $input->getOption('global');

        if ($global && !$user->is_admin) {
            $output->error('--global is only available to admins.');
            $output->setStatusCode(403);
            return 2;
        }

        $user_id = Auth::id();

        if ($global && $user->is_admin) {
            $user_id = null;
        }

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
                ]
            ],
            [
                'not_in' => 'Shortcut name must not match other shortcut or command names.',
            ]
        );

        if ($validator->fails()) {
            $output->writeErrors($validator);
            return 3;
        }

        $shortcut = ShortcutModel::firstOrNew([
            'name' => $name,
            'user_id' => $user_id,
        ]);

        $shortcut->url = $url;
        $shortcut->save();

        $output->alert('Ok.');

        return 0;
    }
}
