<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Auth;
use Mrchimp\Chimpcom\ErrorCode;
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
        $this->setDescription('Sets a note to be visible (or invisible) to other users.');
        $this->addUsage('12');
        $this->addRelated('note:new');
        $this->addRelated('note:show');
        $this->addRelated('note:find');
        $this->addRelated('note:forget');
        $this->addRelated('project');
        $this->addRelated('tag');

        $this->addArgument(
            'ids',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more IDs for the notes to update.'
        );

        $this->addOption(
            'private',
            'p',
            null,
            'Set the note to private. If this is not set, notes will be set to public.'
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

        $ids = array_map(fn ($id) => Id::decode($id), $input->getArgument('ids'));
        $count = Auth::user()->memories()->whereIn('id', $ids)->count();

        if ($count === 0) {
            $output->error(e('That note doesn\'t exist.'));

            return ErrorCode::MODEL_NOT_FOUND;
        }

        Auth::user()->memories()->whereIn('id', $ids)->update([
            'public' => ($input->getOption('private') ? 0 : 1),
        ]);

        $output->alert('Ok.');

        return ErrorCode::OK;
    }
}
