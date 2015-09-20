<?php 
/**
 * Candyman!
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Commands\AbstractCommand;

/**
 * Candyman!
 * @action candyman
 */
class Candyman extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if ($this->input->get(0) == 'candyman'){
      $this->response->error('AAAAAAAAGH!');
    } else {
      $this->response->say('Pussy.');
    }
    
    $this->setAction('normal');
  }

}