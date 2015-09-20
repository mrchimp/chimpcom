<?php 
/**
 * Echo echo echo
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Echo echo echo
 */
class Doecho extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $word = $this->input->getParamString();

        if (!$word) { $word = 'echo'; }

        $this->response->say("$word <span style=\"font-size: 75%\">$word</span> <span style=\"font-size: 50%\">$word</span> <span style=\"font-size: 25%\">$word</span>");
    }

}