<?php
/**
 * Handle password input after 'login username'
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use App\User;
use Mrchimp\Chimpcom\Commands\AbstractCommand;

use Hash;

/**
 * Handle password input after 'login username'
 */
class Password extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if (Auth::check()) {
      $user = Auth::user();
      $this->response->alert('You are already logged in as '.htmlspecialchars($user->name).'. How did you do that?');
      return;
    }

    $username = Session::get('login_username');
    $password = $this->input->get(0);
    Session::forget('login_username');
    $this->setAction('normal');

    if (!$password) {
      $this->response->alert('No password given. Start again.');
      return;
    }

    if (!$username) {
      $this->response->error('I forgot your name, sorry. Start again.');
      return;
    }

    if (Auth::attempt([
      'email' => $username,
      'password' => $password
    ], false, true)) {
      $this->response->getUserDetails();
      $this->response->alert('Welcome back.');
    } else {
      $this->response->error('Hmmmm... No.');
    }
  }

}