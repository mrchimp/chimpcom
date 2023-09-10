<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Traits\ManagesTags;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage tasks
 */
class TaskTag extends Command
{
    use ManagesTags;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('task:tag');
        $this->setDescription('Add or remove one or more tags from one or more tasks.');
        $this->addRelated('task');
        $this->addRelated('task:new');
        $this->addRelated('task:done');
        $this->addRelated('task:edit');
        $this->addRelated('project');
        $this->addRelated('tag');
        $this->addUsage('task:tag 1 2 @bug @urgent');
        $this->addUsage('task:tag --remove 6f @bug @bananas');
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY,
            'A list of task IDs and tags to add/remove. Tags should be prepended with an @.'
        );
        $this->addOption(
            'remove',
            'r',
            null,
            'If set tags will be removed from tasks, otherwise tags will be added to tasks.'
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
            $output->setStatusCode(404);

            return ErrorCode::NOT_AUTHORISED;
        }

        return $this->manageTags($input, $output);
    }

    protected function findItems($ids = [], InputInterface $input): EloquentCollection
    {
        return Auth::user()->tasks()->whereIn('id', $ids)->get();
    }
}
