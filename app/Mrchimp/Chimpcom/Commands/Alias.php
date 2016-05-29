<?php
/**
 * Add a command alias
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Format;

/**
 * Add a command alias
 */
class Alias extends AdminCommand
{

  protected $title = 'Alias';
  protected $description = 'Add an alias for a command.';
  protected $usage = 'alias [&lt;alias&gt; &lt;command&gt;]';
  protected $example = 'alias foo bar';

  /**
   * Run the command
   */
  public function process() {
    if (!$this->input->get(2)) {
        $this->response->error('Alias and command needed.');
        return;
    }

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
