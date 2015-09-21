<?php 
/**
 * Show Chimpcom version number
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * Show Chimpcom version number
 */
class Version extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $this->response->say(Chimpcom::VERSION);
    }

}