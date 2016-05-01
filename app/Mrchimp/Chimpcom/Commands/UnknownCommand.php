<?php
/**
 * Responds to an unknown command
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Format;

/**
 * Responds to an unknown command
 */
class UnknownCommand extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    $this->response->say(Format::error('Unknown command ' . htmlspecialchars($this->input->getCommand())));
    $this->unknownCmd();
    $this->logCmd();
  }

}
