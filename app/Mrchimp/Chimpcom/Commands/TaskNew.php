<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Tag;
use Mrchimp\Chimpcom\Models\Task as TaskModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage tasks
 */
class TaskNew extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('task:new');
        $this->setDescription('Create a new task.');
        $this->setHelp('Task will be attached to the current project unless the --project option is set.');
        $this->addRelated('task');
        $this->addRelated('task:done');
        $this->addRelated('task:edit');
        $this->addRelated('task:tag');
        $this->addRelated('project');
        $this->addRelated('tag');
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY,
            'For NEW, this should be a description of the task.'
        );
        $this->addOption(
            'priority',
            'p',
            InputArgument::OPTIONAL,
            'Priority of the task. Higher is more important. Default is 1.'
        );
        $this->addOption(
            'project',
            null,
            InputArgument::OPTIONAL,
            'Name of project to assign task to.'
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

        return $this->newTask($input, $output);
    }

    protected function newTask(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $content = implode(' ', $input->getArgument('content'));
        [$words, $tags] = $input->splitWordsAndTags($content);
        $description = implode(' ', $words);
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');

            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        $output->write('Description: ' . Format::escape($description) . Format::nl());

        if (!empty($tags)) {
            $output->write('Tags: ' . implode(', ', $tags) . Format::nl());
        }

        $task = TaskModel::create([
            'description' => $description,
            'project_id' => $project->id,
            'user_id' => $user->id,
            'priority' => $input->getOption('priority') ?? 1,
            'completed' => 0,
        ]);

        foreach ($tags as $tag_name) {
            $tag = Tag::firstOrCreate([
                'tag' => $tag_name,
            ]);
            $task->tags()->save($tag);
        }

        $output->alert('Task created. Id: ' . Id::encode($task->id));

        return ErrorCode::OK;
    }
}
