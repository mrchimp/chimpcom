<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Tag;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage tasks
 */
class TaskTag extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('task');
        $this->setDescription('Add or remove one or more tags from one or more tasks.');
        $this->addRelated('task');
        $this->addRelated('task:new');
        $this->addRelated('task:done');
        $this->addRelated('task:edit');
        $this->addRelated('project');
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

    protected function manageTags(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $project = $user->activeProject;
        $remove = $input->getOption('remove');

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        $content = $input->getArgument('content');
        [$words, $tags] = $input->splitWordsAndTags($content);

        if (empty($words)) {
            $output->error('No task IDs provided.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        if (empty($tags)) {
            $output->error('No tags provided.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        $ids = array_map(fn ($word) => Id::decode($word), $words);
        $tasks = $user
            ->tasks()
            ->whereIn('id', $ids);

        if (empty($tasks)) {
            $output->error('No tasks found with the given IDs.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $output->write(($remove ? 'Removing' : 'Adding') . ' tags: ' . implode(', ', $tags) . Format::nl());

        $tag_models = new Collection();

        foreach ($tags as $tag_name) {
            $tag_models->push(Tag::firstOrCreate([
                'tag' => $tag_name,
            ]));
        }

        $tag_ids = $tag_models->pluck('id');

        if ($remove) {
            $tasks->each(fn ($task) => $task->tags()->detach($tag_ids));
        } else {
            $tasks->each(fn ($task) => $task->tags()->syncWithoutDetaching($tag_ids));
        }

        return ErrorCode::SUCCESS;
    }
}
