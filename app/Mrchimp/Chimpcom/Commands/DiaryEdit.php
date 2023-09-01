<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryEdit extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary:edit');
        $this->setDescription('Edit diary entries.');
        $this->addUsage('diary edit --date=yesterday');
        $this->addUsage('diary');
        $this->addUsage('diary:new');
        $this->addUsage('diary:read');
        $this->addUsage('diary:graph');
        $this->addRelated('project');
        $this->addArgument(
            'content',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The diary entry and/or tags.'
        );
        $this->addOption(
            'project',
            'p',
            InputOption::VALUE_REQUIRED,
            'The project to attach the entry to.'
        );
        $this->addOption(
            'date',
            'd',
            InputOption::VALUE_REQUIRED,
            'The date that the entry is for.'
        );
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

        return $this->editEntry($input, $output);
    }

    /**
     * Edit an entry
     */
    protected function editEntry(InputInterface $input, OutputInterface $output): int
    {
        $output->error('Not implemented yet.');

        return ErrorCode::NOT_IMPLEMENTED;
    }
}
