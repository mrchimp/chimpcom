<?php 
/**
 * Show some characters
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Show some characters
 */
class Charmap extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $count = ($this->input->get(1) !== false ? $this->input->get(1) : 128);
        $show_numbers = $this->input->isFlagSet(['--numbers', '-n']);

        for ($x=33; $x<$count+33; $x++) {
            if ($show_numbers) {
                $this->response->say(Format::title($x) . '&#' . $x . '; ');
            } else {
                $this->response->say("&#$x; ");
            }
        }
    }

}