<?php 
/**
 * Get the date
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Get the date
 */
class Date extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        if ($this->input->isFlagSet(['--date', '-d'])) {
            $this->response->say(date('l jS \of F Y'));
        } else if ($this->input->isFlagSet(['--time', '-t'])) {
            $this->response->say(date('h:i:s A'));
        } else if ($this->input->isFlagSet(['--iso', '-i'])) {
            $this->response->say(date('c'));
        } else {
            $this->response->say(date('l jS \of F Y h:i:s A e'));
        }
    }

}