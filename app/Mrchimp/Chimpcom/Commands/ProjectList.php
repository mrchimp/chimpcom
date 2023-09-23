<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage projects
 */
class ProjectList extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('project:list');
        $this->setDescription('Lists your projects.');
        $this->addUsage('project');
        $this->addRelated('task');
        $this->addRelated('priority');
        $this->addRelated('project');
        $this->addRelated('project:new');
        $this->addRelated('project:set');
        $this->addRelated('project:rm');
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        return $this->listProjects($input, $output);
    }

    /**
     * List all user's projects
     */
    protected function listProjects(InputInterface $input, OutputInterface $output): int
    {
        $user = Auth::user();

        // Report result
        if (count($user->projects) === 0) {
            $output->error('No projects.');

            return ErrorCode::OK;
        }

        $output_chunks = [];

        foreach ($user->projects as $project) {
            $output_chunks[] = Format::title(Format::escape($project->name), [
                'data-type' => 'autofill',
                'data-autofill' => 'project ' . $project->id,
            ]) . Format::nl() . $project->description;
        }

        $output->write(implode(Format::nl(), $output_chunks));

        return ErrorCode::OK;
    }
}
