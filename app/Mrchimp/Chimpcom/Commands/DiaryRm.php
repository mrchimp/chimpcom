<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Carbon\Exceptions\InvalidFormatException;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryRm extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary:rm');
        $this->setDescription('Remove diary entries.');
        $this->addUsage('diary:rm --date=yesterday');
        $this->addRelated('diary');
        $this->addRelated('diary:read');
        $this->addRelated('diary:edit');
        $this->addRelated('diary:graph');
        $this->addRelated('project');
        $this->addRelated('tag');
        $this->addOption(
            'date',
            'd',
            InputOption::VALUE_REQUIRED,
            'The date that the entry to delete.'
        );
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Bypass confirmation and skip straight to the deleting. No safety nets.'
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return ErrorCode::NOT_AUTHORISED;
        }

        try {
            $date = $input->dateOption('date');
        } catch (InvalidFormatException $e) {
            $output->error('Invalid date.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        $entry = Auth::user()->diaryEntries()->whereDay('date', $date)->first();

        if (!$entry) {
            $output->error('No entry found for that date.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        if ($input->getOption('force')) {
            $entry->delete();
            $output->alert('Entry deleted.');
            return ErrorCode::OK;
        }

        $output->setAction('diary_rm', [
            'date' => $date,
        ]);
        $output->useQuestionInput();
        $output->alert('Are you sure you want to delete this entry?' . Format::nl());
        $output->write($entry->content . Format::nl());
        $output->write(Format::diaryEntryList(collect([$entry])));
        $output->write(Format::nl() . 'yes/no?');

        return ErrorCode::OK;
    }
}
