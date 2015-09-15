<?php 
/**
 * Log in to Chimpcom
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;
use Session;

/**
 * Log in to Chimpcom
 */
class Login extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if (Auth::check()) {
      $user = Auth::user();
      $this->response->alert('You are already logged in as '.htmlspecialchars($user->name).'.');
      return;
    }

    $username = $this->input->get(1);

    if (!$username) {
      $this->response->alert('Provide a username.');
      return;
    }

    $user = User::where('email', $username)->get();

    // User doesn't exist
    if (count($user) === 0) {
      $this->response->error('You fail. The username '.htmlspecialchars($username).' does not exist. 
                    Create a new account by using the register command.');
      $this->response->cFill("register $username");
      $this->response->usePasswordInput(false);
      $this->setAction('normal');
      return;
    }
    
    Session::set('login_username', trim($username));

    $this->response->alert('Password:');
    $this->response->usePasswordInput();
    $this->setAction('password');
  }

}