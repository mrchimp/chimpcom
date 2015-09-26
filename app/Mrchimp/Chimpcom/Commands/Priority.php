<?php 
/**
 * Set the priority of todo items
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Chimpcom;

/**
 * Set the priority of todo items
 */
class Priority extends LoggedInCommand
{

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();
        $task_id = Chimpcom::decodeId($this->input->get(1));
        $priority = (int)$this->input->get(2);

        if (!is_numeric($priority)) {
            $this->response->error('Priority should be an integer.');
            return false;
        }

        $project = $user->activeProject();

        if (!$project) {
            $this->response->error('No active project. User `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        $task = Task::where('id', $task_id)->where('user_id', $user->id)->first();

        if (!$task) {
            $this->response->error('Couldn\'t find that task, or it\'s not yours to edit.');
            return false;
        }

        $task->priority = $priority;
        
        if ($task->save()) {
            $this->response->alert('Ok.');
        } else {
            $this->response->error('There was a problem. Try again?');
        }
    }

}