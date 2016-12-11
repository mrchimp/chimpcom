<?php
/**
 * List available projects
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List available projects
 */
class Projects extends Command
{
    protected function configure()
    {
        $this->setName('projects');
        $this->setDescription('Lists projects.');
        $this->addRelated('project');
        $this->addRelated('newtask');
        $this->addRelated('todo');
        $this->addRelated('done');
    }

    /**
     * Run the command
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();

        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            return;
        }

        // Report result
        if (count($user->projects) === 0) {
            $output->error('No projects.');
            return false;
        }

        $output_chunks = [];

        foreach ($user->projects as $project) {
            $output_chunks[] = '#'.$project->id . ' <span data-type="autofill" data-autofill="project '.$project->id.'"> ' . e($project->name) . ' - ' . count($project->tasks()) . ' tasks</span>';
        }

        $output->write(implode('<br>', $output_chunks));
    }

}
