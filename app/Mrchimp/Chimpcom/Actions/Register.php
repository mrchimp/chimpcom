<?php
/**
 * Handle password input after 'register'
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use App\User;
use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Commands\AbstractCommand;

/**
 * Handle password input after 'register'
 * @action register2
 * @action normal
 */
class Register extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if (!Session::get('register_username')) {
      $this->response->error('This should not happen.');
      $this->setAction('normal');
      return;
    }

    $password = $this->input->get(0);

    if (!$password) {
      $this->response->error('No password given. Giving up.');
      $this->setAction('normal');
      Session::forget('register_username');
      return;
    }

    session(['register_password' => $password]);
    $this->response->alert('Enter the same password again:');
    $this->setAction('register2');
    $this->response->usePasswordInput(false);
  }

}