<?php
/**
 * Change your password
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;
use Mrchimp\Chimpcom\Commands\LoggedInCommand;

/**
 * Change your password
 */
class Chpass extends LoggedInCommand
{

    protected $title = 'Chpass';
    protected $description = 'Change your password.';

    public function process()
    {
        $this->setAction('chpass_1');
        $this->response->usePasswordInput(true);
        $this->response->alert('Enter your new password. Type cancel to cancel.');
    }

}
