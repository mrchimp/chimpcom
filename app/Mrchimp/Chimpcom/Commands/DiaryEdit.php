<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
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
        $this->addRelated('diary');
        $this->addRelated('diary:new');
        $this->addRelated('diary:read');
        $this->addRelated('diary:graph');
        $this->addRelated('project');
        $this->addRelated('tag');
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
        $date = $input->dateOption('date');
        $user = Auth::user();
        $entry = $user->diaryEntries()->whereDate('date', '=', $date)->first();

        if (!$entry) {
            $output->error('No entry found for that date.');
            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        $output->setAction('diary_edit', [
            'entry_id' => $entry->id,
        ]);
        $output->editContent($entry->content);

        return ErrorCode::OK;
    }
}
