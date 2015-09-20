<?php 
/**
 * Create a new task on the current project
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Models\Task;

/**
 * Create a new task on the current project
 */
class Newtask extends LoggedInCommand
{

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();

        if ($this->input->get(1) === false) {
            $this->error('Do what?');
            return;
        }

        $project = $user->activeProject;

        if (!$project) {
            $this->response->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        $task = new Task();
        $task->description = $this->input->getParamString();
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

        $this->response->alert('Ok.');
    }

}