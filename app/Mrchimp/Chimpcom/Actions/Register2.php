<?php
/**
 * Handle second password input and create account
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use App\User;
use Mrchimp\Chimpcom\Commands\AbstractCommand;

/**
 * Handle second password input and create account
 * @action normal
 */
class Register2 extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $username = Session::get('register_username');
        $password = Session::get('register_password');
        $password2 = $this->input->get(0);

        if (!$username || !$password) {
            $this->response->error('This should not happen.');
            $this->setAction('normal');
            $this->response->usePasswordInput(false);
            Session::forget('register_username');
            Session::forget('register_password');
            return;
        }

        if (!$password2) {
            $this->response->error('No password given. Giving up.');
            $this->resetTerminal();
            return;
        }

        Session::set('register_password2', $password2);
        $this->response->alert('Your email address:');
        $this->setAction('register3');
        $this->response->usePasswordInput(false);
    }

}