<?php
/**
 * Save the description for a new project
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use App\User;
use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Commands\LoggedInCommand;
use Mrchimp\Chimpcom\Models\Project;

/**
 * Save the description for a new project
 * @action normal
 */
class Newproject extends LoggedInCommand
{

  /**
   * Run the command
   */
  public function process() {
  	$user = Auth::user();

    if (!Session::has('current_project_id')) {
      $this->response->error('Oops. There was a problem. [Current project not set.]');
      $this->setAction('normal');
      return false;
    }

    $project = Project::find(Session::get('current_project_id'));

    if (!$project->id) {
      $this->response->error('Current project doesn\'t exist.');
      Session::forget('current_project_id');
    	$this->setAction('normal');
      return false;
    }

    $project->description = $this->input->getInput();
    $project->is_new = false;
    $project->save();

    $project->activeUsers()->save($user);

    $this->response->alert('Project saved and set as current project.');
    $this->setAction('normal');
  }

}