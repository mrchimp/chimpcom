<?php
/**
 * Current user's todo list
 */

namespace Mrchimp\Chimpcom\Commands;

use DB;
use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Task;

/**
 * Current user's todo list
 */
class Todo extends LoggedInCommand
{

    protected $title = 'Todo';
    protected $description = 'Lists tasks on the current project. By default only incomplete tasks from the current project are shown.';
    protected $usage = 'todo [--all|-a] [--allprojects|-p] [--completed|-c]';
    protected $example = 'todo -all';
    protected $see_also = 'project, projects, newtask, done';

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();

        $project = $user->activeProject;

        if (!$project) {
            $this->response->error('No active project. Use `PROJECTS` and `SET PROJECT x`.');
            return;
        }

        $data = [];
        $show_all_items    = $this->input->isFlagSet(['--all', '-a']);
        $show_all_projects = $this->input->isFlagSet(['--allprojects', '-p']);
        $show_completed    = $this->input->isFlagSet(['--completed', '-c']);

        if ($show_all_items) {
            $completion = null;
        } else if ($show_completed) {
            $completion = true;
        } else {
            $completion = false;
        }

        $tasks = Task::where('user_id', $user->id);

        if ($show_all_projects) {
            $this->response->say('Showing task from all projects.<br>');
        } else {
            $this->response->say('Current project: ' . e($project->name) . '<br>');
        }

        $count = 10;
        $search_term = $this->input->getParamString();

        if (is_numeric($this->input->get(1))) {
            $count = (int)$this->input->get(1);
            $search_term = implode(' ', array_slice($this->input->getWordArray(), 2));
        }

        $tasks = $tasks->search($search_term)
            ->project($show_all_projects ? null : $user->activeProject->id)
            ->completed($completion)
            ->orderBy('completed', 'ASC')
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->take($count)
            ->get();

        if (!$tasks) {
            $this->alert('Nothing to do!');
            return false;
        }

        $this->response->say(Format::tasks($tasks));
        $this->response->say('<br>' . count($tasks) . ' tasks.');
    }

}
