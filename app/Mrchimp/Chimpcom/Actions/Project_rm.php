<?php 
/**
 * Delete a project
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Commands\LoggedInCommand;

/**
 * Delete a project
 */
class Project_rm extends LoggedInCommand
{

    /**
     * Run the command
     */
    public function process() {
        $this->setAction('normal');
        $user = Auth::user();

        if (!Booleanate::isAffirmative($this->input->getInput())) {
            $this->response->error('Fair enough.');
            return;
        }

        $project = $user->activeProject;

        if (!$project) {
            $this->response->error('No active project.');
            return;
        }

        if ($project->user_id !== $user->id) {
            $this->response->error('That isn\'t yours to delete.');
            return;
        }

        $project->delete();

        $this->response->alert('Ok. It\'s gone.');
    }

}