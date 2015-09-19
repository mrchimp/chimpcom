<?php 
/**
 * Add a command alias
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;

/**
 * Add a command alias
 */
class Alias extends AdinCommand
{

  /**
   * Run the command
   */
  public function process() {
    $data = [
      'name' => $this->input->get(1),
      'alias' => implode(' ', array_slice($this->input->getParamArray(), 1))
    ];

    $rules = [
      'name'  => 'required|unique:aliases,name',
      'alias' => 'required'
    ];

    if (!$this->validateOrDie($data, $rules)) {
      return;
    }

    $alias = new ChimpcomAlias();
    $alias->name  = $data['name'];
    $alias->alias = $data['alias'];

    if ($alias->save()) {
      $this->response->alert('Ok.');
    } else {
      $this->response->error('There was a problem. Try again.');
    }
  }

}