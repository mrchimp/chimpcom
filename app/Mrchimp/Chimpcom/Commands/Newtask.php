<?php
/**
 * Create a new task on the current project
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a new task on the current project
 */
class Newtask extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('do');
        $this->setDescription('Add a new task to the current project.');
        $this->addRelated('priority');
        $this->addRelated('project');
        $this->addRelated('projects');
        $this->addRelated('todo');
        $this->addRelated('done');
        $this->addArgument(
            'description',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'Description of what needs doing.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must be logged in to use this command.');
            return false;
        }

        $user = Auth::user();
        $description = implode(' ', $input->getArgument('description'));
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        $task = new Task();
        $task->description = $description;
        $user->tasks()->save($task);
        $project->tasks()->save($task);

        // @todo - cross-user tasks
        // $user_ids = array($user->id);
        // foreach ($this->name_array as $name) {
        //     $id = $this->user->getId($name);
        //     if ($id > 0) {
        //         array_push($user_ids, $id);
        //     }
        // }

        // foreach ($user_ids as $user_id) {
        //     $user = \R::load('users', $user_id);
        //     $task->sharedUser[] = $user;
        // }

        // $project_id = \R::store($task);

        $output->alert('Ok.');
    }
}