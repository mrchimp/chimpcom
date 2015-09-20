<?php 
/**
 * Your basic common or garden Chimpcom function
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Chimpcom;

/**
 * Your basic common or garden Chimpcom function
 */
class Hi extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $this->response->say(Chimpcom::welcomeMessage());
    }

}