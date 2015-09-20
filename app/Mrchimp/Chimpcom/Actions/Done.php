<?php

namespace Mrchimp\Chimpcom\Actions;

use DB;
use Auth;
use Session;
use App\User;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Models\Task;
use Mrchimp\Chimpcom\Commands\LoggedInCommand;

class Done extends LoggedInCommand
{

    public function process() {
        $user = Auth::user();
        $confirmed = Booleanate::isAffirmative($this->input->get(0));
        $project = $user->activeProject;

        if (!$project) {
            $this->response->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        if (!$confirmed) {
            $this->response->say('Fair enough.');
            $this->setAction();
            Session::forget('task_to_complete');
            return;
        }

        $task = Task::where('id', Session::get('task_to_complete'))
                    ->where('project_id', $project->id)
                    ->first();

        if (!$task) {
            $this->response->error('Couldn\'t find that task.');
            return;
        }

        $task->completed = true;
        $task->time_completed = DB::raw('now()');
        $task->save();

        $this->setAction();
        $this->response->alert('Ok.');
    }

}