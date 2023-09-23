<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Carbon\Carbon;
use Mrchimp\Chimpcom\ErrorCode;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryRm extends Action
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary_rm');
        $this->setDescription('Delete a diary entry.');
        $this->addArgument(
            'confirmation',
            InputArgument::REQUIRED,
            'A yes or no-like answer.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = Carbon::parse($input->getActionData('date'));
        $entry = Auth::user()->diaryEntries()->whereDay('date', $date)->first();

        if (!$entry) {
            $output->error('No entry found for that date.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $entry->delete();

        $output->write('Diary entry deleted.');

        return ErrorCode::OK;
    }
}
