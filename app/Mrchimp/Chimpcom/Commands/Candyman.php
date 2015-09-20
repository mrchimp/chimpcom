<?php 
/**
 * Candyman!
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Candyman!
 * @action candyman
 */
class Candyman extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    $this->setAction('candyman');
    $this->response->say('candyman');
  }

}