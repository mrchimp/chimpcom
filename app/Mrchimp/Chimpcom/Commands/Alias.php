<?php 
/**
 * Add a command alias
 */

namespace Mrchimp\Chimpcom\Commands;

/**
 * Add a command alias
 */
class Alias extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    
    $this->response->say('Not yet.');
    // if (!$this->user->isAdmin()) {
    //   $this->error('No.');
    //   return false;
    // }
    
    // $alias = \R::dispense('alias');
    // $alias->name  = $this->inputArray(1);
    // $alias->alias = implode(' ', array_slice($this->input_array, 2));
    // \R::store($alias);
    
    // $this->alert('Ok.');
  }

}