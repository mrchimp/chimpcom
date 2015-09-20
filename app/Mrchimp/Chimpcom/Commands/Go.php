<?php 
/**
 * Go to a given URL
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * Go to a given URL
 */
class Go extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        if (substr($this->input->getParamString(), 0, 4) !== 'http') {
            $this->response->redirect('http://'.$this->input->getParamString());
        } else {
            $this->response->redirect($this->input->getParamString());
        }
    }

}