<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Auth;
use Mrchimp\Chimpcom\Input;

abstract class LoggedInCommand extends Command {

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $this->response->error('You must be logged in to use this command.');
            return $this->response;
        }
        parent::run($input);
        return $this->response;
    }

    public function runTabcomplete(Input $input)
    {
        if (!Auth::check()) {
            return '';
        }

        return parent::runTabcomplete($input);
    }

}
