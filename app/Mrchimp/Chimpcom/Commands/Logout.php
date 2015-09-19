<?php 
/**
 * Log out of Chimpcom
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;
use Session;

/**
 * Log out of Chimpcom
 */
class Logout extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        if (!Auth::check()) {
            $this->response->error('You\'re not logged in.');
            return;
        }

        Auth::logout();
        $this->response->getUserDetails();
        $this->response->alert('You are now logged out.');
    }

}