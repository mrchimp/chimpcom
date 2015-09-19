<?php 
/**
 * Takes a username and asks for a password
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Session;
use Mrchimp\Chimpcom\Format;

/**
 * Takes a username and asks for a password
 * @action register
 */
class Register extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if (Auth::check()) {
      $this->response->error('You\'re already logged in.');
      return;
    }
    
    $username = $this->input->get(1);

    if (!$username) {
      $this->response->error('You should provide a new username.');
      return;
    }

    Session::set('register_username', $username);
    $this->setAction('register');
    $this->response->usePasswordInput(true);
    $this->response->alert('Enter a password:');
  }

}