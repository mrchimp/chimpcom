<?php 
/**
 * Change directory
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Change directory
 */
class Cd extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        if ($this->input->get(1) == 'penguin'){
            $this->response->say('You are inside a penguin. It is dark.');
        } else if ($this->input->get(1) == 'c:' || $this->input->get(1) == 'C:') {
            $this->response->say('What d\'you think this is, Windows?');
        } else if ($this->input->get(1) == '..'){
            $this->response->say('You claw at the directory above you but cannot get a purchase.');
        } else {
            if ($this->input->get(1)) {
                $this->unknownCmd();
            }
            $this->response->say('You remain here.');
        }
    }

}