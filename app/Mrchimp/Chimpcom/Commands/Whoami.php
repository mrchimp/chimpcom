<?php 
/**
 * Get current username
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Get current username
 */
class Whoami extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if (Auth::check()) {
      $this->response->say(Auth::user()->name);
    } else {
      $this->response->say('Guest');
    }
  }

}