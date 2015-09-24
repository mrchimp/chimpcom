<?php 
/**
 * A finite amount of artificial monkeys
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * A finite amount of artificial monkeys
 */
class Monkeys extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $this->response->title('Chimpcom Infinite Monkey Shakespeare Project<br>');

        // Define an alphabet to choose letters from.
        // I think I added extra spaces so that it spaces the 
        // words the words out semi-realistically
        $alphabet = 'abcdefghijklmnopqrstuvwxyz    ';

        // Count the letters of the alphabet so that we don't 
        // have to do it in a loop below.
        $size     = strlen($alphabet);
        $target   = 'to be or not to be';
        $apechat  = '';

        for ($x = 1; $x < 1000; $x++) {
            $rand = rand(0, $size-1);
            $apechat  = $apechat  . $alphabet[$rand];
        }

        // First, check if we've got the whole thing.
        if (strpos($apechat, $target) !== false) {
            $apechat = str_replace($target, $this->alert($target, [], true), $apechat);
            $this->response->alert('<br>SUCCESS!<br><br>');
            $this->response->say($apechat);
            return true;
        }

        $search  = ['to',
                   'be',
                   'or',
                   'not'];

        $replace = ['<span class="blue_highlight">to</span>',
                    '<span class="blue_highlight">be</span>',
                    '<span class="blue_highlight">or</span>',
                    '<span class="blue_highlight">not</span>'];
        
        $apechat  = str_replace($search, $replace, $apechat);
        
        // Output the a title and the string.
        $this->response->say($apechat);
    }

}
