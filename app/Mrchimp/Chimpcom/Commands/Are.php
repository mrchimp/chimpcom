<?php 
/**
 * Get some answers
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Get some answers
 */
class Are extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if (substr($this->input->getInput(), -1) != '?'){
      $this->response->say('Questions end with question marks.');
      return;
    }

    if ($this->input->get(1) == 'you') {
      if ($this->input->get(2) == 'sentient?') {
        $this->response->say('Pretty much.');
      } else if ($this->input->get(2) == 'human?') {
        $this->response->say('What does it look like?');
      }
      return;
    }

    $answers = ['I\'m not sure yet. ',
                'No way. ',
                'Definitely. ',
                'It depends on your point of view. '];

    $rand = floor(rand(0,count($answers) - 1));
    $this->response->say($answers[$rand]);
  }

}