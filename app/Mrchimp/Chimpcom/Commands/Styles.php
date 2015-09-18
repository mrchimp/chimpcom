<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Format;

class Styles extends AbstractCommand
{
  public function process() {
    $this->response->say(Format::title('This Is A Title<br>'));
    $this->response->say('Here\'s some regular text (say)<br>');
    $this->response->say(Format::alert('This is an alert!<br>'));
    $this->response->say(Format::error('Oh no! This is an error!<br>'));
    $this->response->say('<code>$this === some($code)</code><br>');
    $this->response->say(Format::style('Auto fill (click me)', '', [
      'data-type' => 'autofill',
      'data-autofill' => 'you clicked an autofill'
    ]));

    // @todo - Make this work
    $this->response->cFill('This text was automatically inserted');
  }
}