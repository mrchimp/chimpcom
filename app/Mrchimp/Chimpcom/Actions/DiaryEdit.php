<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Chimpcom;
use Mrchimp\Chimpcom\ErrorCode;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryEdit extends Action
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary_edit');
        $this->setDescription('Handle task edited content and save it');
    }



    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entry_id = $input->getActionData('entry_id');

        Chimpcom::delAction($input->getActionId());

        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            return 1;
        }

        $user = Auth::user();
        $entry = $user->diaryEntries()->where('id', $entry_id)->first();

        if (!$entry) {
            $output->error('Entry not found.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $entry->content = $input->getContent();
        $entry->save();

        $output->alert('Ok.');

        return ErrorCode::OK;
    }
}
