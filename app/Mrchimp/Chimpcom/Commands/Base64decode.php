<?php 
/**
 * Decode a string in base64
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Decode a string in base64
 */
class Base64decode extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    $cypher = base64_decode($this->input->getParamString());
    $this->response->say(e($cypher));
  }

}