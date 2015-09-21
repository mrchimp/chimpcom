<?php 
/**
 * Your basic common or garden Chimpcom function
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * Your basic common or garden Chimpcom function
 */
class Tea extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
    	if ($this->input->get(1) == false) {
            $this->response->error('I\'m gonna need some names.');
            return;
        }

        $names = $this->input->getParamArray();
        $rand = array_rand($names);
        $this->response->say(ucwords($this->input->getParamArray()[$rand]).' is on hot beverage duty.');
    }

}