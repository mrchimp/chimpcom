<?php

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\File;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Edit extends Action
{
    protected function configure()
    {
        $this->setName('edit');
        $this->setDescription('Handle edited content and save it');
        $this->addArgument('content', InputArgument::OPTIONAL, 'Content to save.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Chimpcom::setAction('normal');

        $file = File::find(Session::get('edit_id'));

        if (!$file) {
            $output->write('File got lost along the way. Try again.');
            return 1;
        }

        $file->content = $input->getArgument('content');
        $file->save();

        $output->alert('Ok.');

        return 0;
    }
}
