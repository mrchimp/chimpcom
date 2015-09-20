<?php 
/**
 * Encode a string in base64
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Encode a string in base64
 */
class Base64encode extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    $cypher = base64_encode($this->input->getParamString());
    $this->response->say(e($cypher));
  }

}