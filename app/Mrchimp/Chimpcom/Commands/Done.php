<?php
/**
 * Give credit where it's due
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Session;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Task;

/**
 * Mark a task as done
 */
class Done extends LoggedInCommand
{

    protected $title = 'Done';
    protected $description = 'Marks a task as completed.';
    protected $usage = 'done &lt;task_id&gt;';
    protected $example = 'done 12';
    protected $see_also = 'project, projects, newtask, todo';

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();
        $project = $user->activeProject;

        if (!$project) {
            $this->response->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        $task_id = Chimpcom::decodeId($this->input->get(1));

        $task = Task::where('id', $task_id)
                    ->where('project_id', $project->id)
                    ->first();

        if (!$task) {
            $this->response->error('Couldn\'t find that task.');
            return false;
        }

        Session::set('task_to_complete', $task->id);

        $this->response->alert('Are you sure you want to delete this task?<br>');
        $this->response->say($task->description);

        $this->setAction('done');
    }

}
