<?php

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Actions\Action;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Edit extends Action
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('edit');
        $this->setDescription('Handle edited content and save it');
        $this->addOption('continue', 'c', null, 'Continue editing');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('continue')) {
            Chimpcom::delAction($input->getActionId());
        } else {
            $action = $input->getAction();
            $output->setAction(
                $action['action_name'],
                $action['data'],
            );
        }

        $file = File::find($input->getActionData('edit_id'));

        if (!$file) {
            $output->error('File got lost along the way. Try again.');
            return 1;
        }

        $content = $input->getContent();

        if (empty($content)) {
            $output->error('No content to save. Aborting.');
            return 2;
        }

        $file->content = $content;
        $file->save();

        if (!$input->getOption('continue')) {
            $output->alert('Ok.');
        }

        return 0;
    }
}
