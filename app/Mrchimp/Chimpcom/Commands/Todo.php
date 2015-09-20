<?php 
/**
 * Current user's todo list
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Task;

/**
 * Current user's todo list
 */
class Todo extends LoggedInCommand
{

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
        $show_completed    = (!$show_completed ? null : true);

        if ($show_all_projects) {
            $this->response->say('Showing task from all projects.<br>');
            $tasks = Task::where('user_id', $user->id);
        } else {
            $this->response->say('Current project: ' . e($project->name) . '<br>');
            $tasks = Task::where('user_id', $user->id)
                         ->where('project_id', $user->activeProject->id);
        }
        
        $count = 10;
        $search_term = $this->input->getParamString();

        if (is_numeric($this->input->get(1))) {
            $count = (int)$this->input->get(1);
            $search_term = implode(' ', array_slice($this->input->getParamString(), 1));
        }

        $tasks = $tasks->search($search_term)
            ->completed($show_completed)
            ->orderBy('priority', 'DESC')
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