<?php

/**
 * Bypass command. Allows you to specify the command output as a 
 * string, rather than processing stuff.
 */

namespace Mrchimp\Chimpcom\Commands;

/**
 * Bypasses processing and formats cmd_in and cmd_out properly...
 * Essentially fakes respond(). Used for errors etc.
 */
class BypassCommand extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    $this->response->sayCmdIn(htmlspecialchars($command));
    $this->response->say($output);
    $this->unknownCmd();
    $this->logCmd();
  }

}