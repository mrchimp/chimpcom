<?php
/**
 * Current user's todo list
 */

namespace Mrchimp\Chimpcom\Commands;

use DB;
use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Task;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Current user's todo list
 */
class Todo extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('todo');
        $this->setDescription('Lists tasks on the current project.');
        $this->setHelp('By default only incomplete tasks from the current project are shown.');
        $this->addRelated('project');
        $this->addRelated('projects');
        $this->addRelated('newtask');
        $this->addRelated('done');
        $this->addRelated('priority');
        $this->addArgument(
            'searchterm',
            InputArgument::IS_ARRAY,
            'Show only tasks that contain this.'
        );
        $this->addOption(
            'all',
            'a',
            null,
            'List complete and incomplete tasks.'
        );
        $this->addOption(
            'allprojects',
            'p',
            null,
            'List tasks from all of your projects.'
        );
        $this->addOption(
            'completed',
            'c',
            null,
            'List completed tasks.'
        );
        $this->addOption(
            'num_tasks',
            'n',
            InputOption::VALUE_REQUIRED,
            'Number of tasks to show.',
            10
        );
    }

    /**
     * Run the command
     *
     * @todo add ability to show dates
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must be logged in to use this command.');
            return false;
        }

        $user = Auth::user();

        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `SET PROJECT x`.');
            return;
        }

        $data = [];
        $show_all_items    = $input->getOption('all');
        $show_all_projects = $input->getOption('allprojects');
        $show_completed    = $input->getOption('completed');

        if ($show_all_items) {
            $completion = null;
        } else if ($show_completed) {
            $completion = true;
        } else {
            $completion = false;
        }

        $tasks = Task::where('user_id', $user->id);

        if ($show_all_projects) {
            $output->write('Showing task from all projects.<br>');
        } else {
            $output->write('Current project: ' . e($project->name) . '<br>');
        }

        $num_tasks = $input->getOption('num_tasks');

        $search_term = implode(' ', $input->getArgument('searchterm'));

        $tasks = $tasks->search($search_term)
            ->project($show_all_projects ? null : $user->activeProject->id)
            ->completed($completion)
            ->orderBy('completed', 'ASC')
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->take($num_tasks)
            ->get();

        if (!$tasks) {
            $output->alert('Nothing to do!');
            return false;
        }

        $output->write(Format::tasks($tasks));
        $output->write('<br>' . count($tasks) . ' tasks.');
    }

}
