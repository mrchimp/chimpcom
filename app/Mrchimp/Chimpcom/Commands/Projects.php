<?php
/**
 * List available projects
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Project;

/**
 * List available projects
 */
class Projects extends LoggedInCommand
{

    protected $title = 'Projects';
    protected $description = 'Lists projects.';
    protected $usage = 'projects';
    protected $example = 'projects';
    protected $see_also = 'project, newtask, todo, done';

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();
        $projects = $user->projects;

        // Report result
        if (count($projects) === 0) {
            $this->response->error('No projects.');
            return false;
        }

        $output_chunks = [];

        foreach ($projects as $project) {
            $output_chunks[] = '#'.$project->id . ' <span data-type="autofill" data-autofill="project '.$project->id.'"> ' . e($project->name) . ' - ' . count($project->tasks()) . ' tasks</span>';
        }

        $this->response->say(implode('<br>', $output_chunks));
    }

}
