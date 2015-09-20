<?php 
/**
 * Answer questions beginning with "does"
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Answer questions beginning with "does"
 */
class Does extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        if (substr($this->input->getInput(), -1) != '?'){
            $this->response->say('Questions end with question marks.');
            return;
        }

        $answers = ['I\'m not sure yet. ',
                    'Sometimes. ',
                    'Usually. ',
                    'Sort of. ',
                    'It depends how you look at it. '];

        $rand = floor(rand(0,count($answers) - 1));

        $this->response->say($answers[$rand]);
    }

}