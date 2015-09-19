<?php 
/**
 * Create a memory item
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Tag;

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

    $this->response->say('Name: ' . e($name) . '<br>');
    $this->response->say('Content: ' . e($content) . '<br>');

    $is_public = $this->input->isFlagSet(['--public', '-p']);
    $user = Auth::user();

    $memory = new Memory();
    $memory->name = $name;
    $memory->content = $content;
    $memory->user_id = $user->id;
    $memory->public = $is_public;

    if (!$memory->save()) {
      $this->response->error('Could not save memory. Try again.');
    }

    foreach ($this->input->getTags() as $tag_word) {
      $tag = new Tag();
      $tag->tag = $tag_word;
      $memory->tags()->save($tag);
    }

    $this->response->alert('Memory saved. Id: ' . $memory->id);
  }

}