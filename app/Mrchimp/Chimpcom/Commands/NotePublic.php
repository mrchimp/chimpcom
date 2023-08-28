<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make memories public/private
 */
class NotePublic extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('note:public');
        $this->setDescription('Sets a memory to be visible (or invisible) to other users.');
        $this->addUsage('12');
        $this->addRelated('note:new');
        $this->addRelated('note:show');
        $this->addRelated('note:find');
        $this->addRelated('note:forget');

        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'ID of the memory to update.'
        );

        $this->addOption(
            'private',
            'p',
            null,
            'Set the memory to private.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return ErrorCode::NOT_AUTHORISED;
        }

        $id = Id::decode($input->getArgument('id'));
        $memory = Memory::find($id);

        if (!$memory) {
            $output->error(e('That memory doesn\'t exist.'));

            return ErrorCode::MODEL_NOT_FOUND;
        }

        if (!$memory->isMine()) {
            $output->error(Format::escape('That isn\'t your memory to change.'));

            return ErrorCode::NOT_AUTHORISED;
        }

        $memory->public = ($input->getOption('private') ? 0 : 1);
        $memory->save();

        $output->alert('Ok.');

        return ErrorCode::SUCCESS;
    }
}
