<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List available projects
 */
class Projects extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('projects');
        $this->setDescription('Lists projects.');
        $this->addRelated('project');
        $this->addRelated('newtask');
        $this->addRelated('todo');
        $this->addRelated('done');
        $this->addRelated('priority');
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();

        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        // Report result
        if (count($user->projects) === 0) {
            $output->error('No projects.');

            return 0;
        }

        $output_chunks = [];

        foreach ($user->projects as $project) {
            $output_chunks[] = '#' . $project->id . ' <span data-type="autofill" data-autofill="project ' . $project->id . '"> ' . Format::escape($project->name) . ' - ' . count($project->tasks) . ' tasks</span>';
        }

        $output->write(implode(Format::nl(), $output_chunks));

        return 0;
    }
}
