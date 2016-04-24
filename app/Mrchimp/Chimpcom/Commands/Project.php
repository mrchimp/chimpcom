<?php
/**
 * Manage projects
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Session;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Project as ProjectModel;

/**
 * Manage projects
 */
class Project extends LoggedInCommand
{

    protected $title = 'Project';
    protected $description = 'Sets the active project or shows details of the active project.';
    protected $usage = 'project<br>project set &lt;project_id&gt;<br>project set &lt;project_name&gt;<br>project new &lt;project_name&gt;';
    protected $example = 'project set chimpcom';
    protected $see_also = 'projects, newtask, todo, done';

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();

        // Create new project
        if ($this->input->get(1) === 'new') {
            $name = implode(' ', array_slice($this->input->getParamArray(), 1));

            if (!$name) {
                $this->response->error('No name given.');
                return;
            }

            $project = new ProjectModel();
            $project->is_new  = true;
            $project->name    = $name;
            $user->projects()->save($project);

            Session::set('current_project_id', $project->id);

            $user->setAttribute('project_id', 'int', $project->id);

            $this->response->say('Creating project "'.$project->name.'"...<br>');
            $this->response->say('Please add a description:');
            $this->setAction('newproject');

            return;
        }

        // Set current project
        if ($this->input->get(1) === 'set') {
            $project_id = $this->input->get(2);

            if (is_numeric($project_id)) {
                $project = ProjectModel::where('id', $project_id)
                                       ->where('user_id', $user->id)->first();
            } else {
                $project = ProjectModel::where('name', $project_id)
                                       ->where('user_id', $user->id)->first();
            }

            if (!$project->id) {
                $this->response->error('That project ID is invalid.');
                return;
            }

            $project->activeUsers()->save($user);
            $this->response->alert(e($project->name) . ' is now the current project.');

            return;
        }

        $project = $user->activeProject;

        if (!$project) {
            $this->response->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        // @todo - multiple users
        // List project users
        // if ($this->input->get(1) === 'users') {
        //     if (!$this->project->sharedUser) {
        //         $this->say('<br>no users.');
        //         return true;
        //     }

        //     foreach ($this->project->sharedUser as $user){
        //         $this->say($user->name.'<br>');
        //     }

        //     return true;
        // }

        // remove project etc
        if ($this->input->get(1) === 'rm') {
            $this->response->title('Are you sure you want to delete the project `' . e($project->name) . '`?');
            $this->setAction('project_rm');
            return;
        }

        // Show info about current project
        $this->response->say('Current project: '.$project->name);
    }

    public function tabcomplete() {
        $user = Auth::user();

        if ($this->input->get(1) === 'set') {
            $projects = $user->projects()->select('name')->get();

            foreach ($projects as $project) {
                if (strrpos($project->name, $this->input->get(2))) {
                    return 'project set ' . $project->name;
                }
            }
        }

        return '';
    }

}
