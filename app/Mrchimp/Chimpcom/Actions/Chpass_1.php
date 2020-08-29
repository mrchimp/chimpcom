<?php

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Commands\LoggedInCommand;
use Session;

/**
 * @todo Update!
 */
class Chpass_1 extends LoggedInCommand
{
    public function process()
    {
        $password = $this->input->get(0);

        if (!$password || $password === 'cancel') {
            $this->response->error('Abandoning.');
            $this->response->usePasswordInput(false);
            $this->setAction('normal');
            return;
        }

        Session::put('chpass_1', $password);
        $this->setAction('chpass_2');
        $this->response->usePasswordInput(true);
        $this->response->alert('Enter password again. Type cancel to cancel.');
    }
}
