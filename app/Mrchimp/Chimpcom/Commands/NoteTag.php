<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Traits\ManagesTags;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NoteTag extends Command
{
    use ManagesTags;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('note:tag');
        $this->setDescription('Add or remove one or more tags from one or more notes.');
        $this->addRelated('note');
        $this->addRelated('note:save');
        $this->addRelated('note:find');
        $this->addRelated('note:forget');
        $this->addRelated('note:public');
        $this->addRelated('project');
        $this->addRelated('tag');
        $this->addUsage('note:tag 1 2 @foo @bar');
        $this->addUsage('note:tag --remove 6f @foo @bar');
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY,
            'A list of task IDs and tags to add/remove. Tags should be prepended with an @.'
        );
        $this->addOption(
            'remove',
            'r',
            null,
            'If set tags will be removed from notes, otherwise tags will be added to notes.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            $output->setStatusCode(404);

            return ErrorCode::NOT_AUTHORISED;
        }

        return $this->manageTags($input, $output);
    }

    protected function findItems($ids = [], InputInterface $input): EloquentCollection
    {
        return Auth::user()->memories()->whereIn('id', $ids)->get();
    }
}
