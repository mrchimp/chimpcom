<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Input;

abstract class LoggedInCommand extends AbstractCommand {

  public function run(Input $input) {
    if (!Auth::check()) {
      $this->response->error('You must be logged in to use this command.');
      return $this->response;
    }
    parent::run($input);
    return $this->response;
  }

}