<?php

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Edit extends Action
{
    protected function configure()
    {
        $this->setName('edit');
        $this->setDescription('Handle edited content and save it');
        $this->addOption('continue', 'c', null, 'Continue editing');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('continue')) {
            Chimpcom::setAction('normal');
        }

        $file = File::find(Session::get('edit_id'));

        if (!$file) {
            $output->write('File got lost along the way. Try again.');
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
