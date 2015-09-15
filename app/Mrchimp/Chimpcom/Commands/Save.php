<?php 
/**
 * Create a memory item
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Memories;

/**
 * Create a memory item
 */
class Save extends LoggedInCommand
{

  /**
   * Run the command
   */
  public function process() {
    $num_params = count($this->input->getParamArray());

    if ($num_params < 2) {
      $this->response->error('Gonna need more than that.');
      return false;
    }

    $content = '';
    $name = $this->input->get(1);
    $content = implode(' ', array_slice($this->input->getParamArray(), 1));

    $this->response->say('name: ' . $name . '<br>');
    $this->response->say('content: ' . $content . '<br>');

    $is_public = $this->input->isFlagSet(['--public', '-p']);
    $user = Auth::user();

    $memory = new Memory();
    $memory->name = $name;
    $memory->content = $content;
    $memory->user_id = $user->id;
    $memory->public = $is_public;

    if ($id = $memory->save()) {
      $this->reponse->alert("Memory saved. Id: $id");
    } else {
      $this->response->error('Could not save memory. Try again.');
    }
  }

}